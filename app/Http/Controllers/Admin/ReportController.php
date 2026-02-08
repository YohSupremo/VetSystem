<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\BillingInvoice;
use App\Models\BillingPayment;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\PetOwner;
use App\Models\InventoryItem;
use App\Models\MedicationDispensing;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Vaccination;
use App\Models\Surgery;
use App\Models\GroomingAppointment;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends BaseController
{
    /**
     * Display a listing of reports.
     */
    public function index()
    {
        $reports = Report::with('generatedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $reportTypeLabels = $this->reportTypeLabels();

        $totalReports = Report::count();
        $financialReports = Report::where('report_type', 'financial')->count();
        $medicalReports = Report::where('report_type', 'medical')->count();
        $inventoryReports = Report::where('report_type', 'inventory')->count();

        return view('admin.reports.index', compact(
            'reports',
            'totalReports',
            'financialReports',
            'medicalReports',
            'inventoryReports',
            'reportTypeLabels'
        ));
    }

    /**
     * Show the form for creating a new report.
     */
    public function create()
    {
        $reportTypeLabels = $this->reportTypeLabels();
        $reportTypes = array_keys($reportTypeLabels);
        return view('admin.reports.create', compact('reportTypes', 'reportTypeLabels'));
    }

    /**
     * Store a newly created report in storage.
     */
    public function store(Request $request)
    {
        $reportTypes = array_keys($this->reportTypeLabels());

        $data = $request->validate([
            'report_type' => 'required|in:' . implode(',', $reportTypes),
            'title' => 'required|string|max:150',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $report = Report::create([
            'report_type' => $data['report_type'],
            'title' => $data['title'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => 'ready',
            'notes' => $data['notes'] ?? null,
            'generated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.reports.show', $report->id)
            ->with('success', 'Report created successfully.');
    }

    /**
     * Display the specified report.
     */
    public function show($id)
    {
        $report = Report::with('generatedBy')->findOrFail($id);
        $reportTypeLabels = $this->reportTypeLabels();
        return view('admin.reports.show', compact('report', 'reportTypeLabels'));
    }

    /**
     * Show the form for editing the specified report.
     */
    public function edit($id)
    {
        $report = Report::findOrFail($id);
        $reportTypeLabels = $this->reportTypeLabels();
        $reportTypes = array_keys($reportTypeLabels);

        return view('admin.reports.edit', compact('report', 'reportTypes', 'reportTypeLabels'));
    }

    /**
     * Update the specified report in storage.
     */
    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $reportTypes = array_keys($this->reportTypeLabels());

        $data = $request->validate([
            'report_type' => 'required|in:' . implode(',', $reportTypes),
            'title' => 'required|string|max:150',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $report->update([
            'report_type' => $data['report_type'],
            'title' => $data['title'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('admin.reports.show', $report->id)
            ->with('success', 'Report updated successfully.');
    }

    /**
     * Remove the specified report from storage.
     */
    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report deleted successfully.');
    }

    private function reportTypeLabels(): array
    {
        return [
            'financial' => 'Financial',
            'medical' => 'Medical',
            'inventory' => 'Inventory',
            'client' => 'Customer',
            'appointment' => 'Operational',
        ];
    }
    
    /**
     * Generate financial reports.
     */
    public function financialReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $reportType = $request->input('report_type', 'summary');
        
        $query = BillingInvoice::whereBetween('invoice_date', [$startDate, $endDate]);
        
        // Summary data
        $totalInvoices = $query->count();
        $totalRevenue = $query->sum('total_amount');
        $paidAmount = $query->sum('paid_amount');
        $outstandingAmount = $totalRevenue - $paidAmount;
        
        // Revenue by month
        $revenueByMonth = BillingInvoice::selectRaw('DATE_FORMAT(invoice_date, "%Y-%m") as month, SUM(total_amount) as revenue')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Top services
        $topServices = DB::table('billing_invoice_items')
            ->select('item_type', DB::raw('COUNT(*) as count, SUM(total_price) as total'))
            ->join('billing_invoices', 'billing_invoice_items.invoice_id', '=', 'billing_invoices.id')
            ->whereBetween('billing_invoices.invoice_date', [$startDate, $endDate])
            ->groupBy('item_type')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
        
        // Payment methods
        $paymentMethods = BillingPayment::select('payment_method', DB::raw('COUNT(*) as count, SUM(amount) as total'))
            ->join('billing_invoices', 'billing_payments.invoice_id', '=', 'billing_invoices.id')
            ->whereBetween('billing_payments.payment_date', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();
        
        // Outstanding invoices
        $outstandingInvoices = BillingInvoice::with(['pet', 'petOwner'])
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->limit(20)
            ->get();
        
        return view('admin.reports.financial', compact(
            'startDate',
            'endDate',
            'reportType',
            'totalInvoices',
            'totalRevenue',
            'paidAmount',
            'outstandingAmount',
            'revenueByMonth',
            'topServices',
            'paymentMethods',
            'outstandingInvoices'
        ));
    }
    
    /**
     * Generate medical reports.
     */
    public function medicalReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $reportType = $request->input('report_type', 'summary');
        
        // Appointments summary
        $totalAppointments = Appointment::whereBetween('appointment_date', [$startDate, $endDate])->count();
        $completedAppointments = Appointment::whereBetween('appointment_date', [$startDate, $endDate])
            ->where('status', 'completed')->count();
        
        // Medical records
        $totalMedicalRecords = MedicalRecord::whereBetween('visit_date', [$startDate, $endDate])->count();
        
        // Prescriptions
        $totalPrescriptions = Prescription::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // Vaccinations
        $totalVaccinations = Vaccination::whereBetween('vaccination_date', [$startDate, $endDate])->count();
        
        // Surgeries
        $totalSurgeries = Surgery::whereBetween('scheduled_date', [$startDate, $endDate])->count();
        
        // Common diagnoses (from medical records)
        $commonDiagnoses = MedicalRecord::select('diagnosis', DB::raw('COUNT(*) as count'))
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->whereNotNull('diagnosis')
            ->groupBy('diagnosis')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        
        // Common treatments
        $commonTreatments = MedicalRecord::select('treatment_plan', DB::raw('COUNT(*) as count'))
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->whereNotNull('treatment_plan')
            ->groupBy('treatment_plan')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        
        // Pet types treated
        $petTypes = Pet::select('species', DB::raw('COUNT(*) as count'))
            ->join('medical_records', 'pets.id', '=', 'medical_records.pet_id')
            ->whereBetween('medical_records.visit_date', [$startDate, $endDate])
            ->groupBy('species')
            ->orderBy('count', 'desc')
            ->get();
        
        return view('admin.reports.medical', compact(
            'startDate',
            'endDate',
            'reportType',
            'totalAppointments',
            'completedAppointments',
            'totalMedicalRecords',
            'totalPrescriptions',
            'totalVaccinations',
            'totalSurgeries',
            'commonDiagnoses',
            'commonTreatments',
            'petTypes'
        ));
    }
    
    /**
     * Generate inventory reports.
     */
    public function inventoryReport(Request $request)
    {
        $reportType = $request->input('report_type', 'summary');
        
        // Current inventory status
        $totalItems = InventoryItem::count();
        $lowStockItems = InventoryItem::whereColumn('quantity', '<=', 'min_stock')->count();
        $expiredItems = InventoryItem::where('expiry_date', '<', now())->count();
        $expiringSoonItems = InventoryItem::where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(30))
            ->count();
        
        // Inventory value
        $totalValue = InventoryItem::selectRaw('SUM(quantity * unit_price) as total_value')
            ->first()
            ->total_value ?? 0;
        
        // Low stock items
        $lowStockList = InventoryItem::with('supplier')
            ->whereColumn('quantity', '<=', 'min_stock')
            ->orderBy('quantity')
            ->limit(20)
            ->get();
        
        // Expired items
        $expiredList = InventoryItem::with('supplier')
            ->where('expiry_date', '<', now())
            ->orderBy('expiry_date')
            ->limit(20)
            ->get();
        
        // Expiring soon items
        $expiringSoonList = InventoryItem::with('supplier')
            ->where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(30))
            ->orderBy('expiry_date')
            ->limit(20)
            ->get();
        
        // Medication dispensing trends (last 30 days)
        $dispensingTrends = MedicationDispensing::selectRaw('DATE(dispensed_at) as date, COUNT(*) as count, SUM(total_price) as total')
            ->where('dispensed_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Top dispensed medications
        $topMedications = MedicationDispensing::select('inventory_item_id', DB::raw('COUNT(*) as count, SUM(quantity_dispensed) as total_quantity'))
            ->with('inventoryItem:name')
            ->where('dispensed_at', '>=', now()->subDays(30))
            ->groupBy('inventory_item_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.reports.inventory', compact(
            'reportType',
            'totalItems',
            'lowStockItems',
            'expiredItems',
            'expiringSoonItems',
            'totalValue',
            'lowStockList',
            'expiredList',
            'expiringSoonList',
            'dispensingTrends',
            'topMedications'
        ));
    }
    
    /**
     * Generate client reports.
     */
    public function clientReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $reportType = $request->input('report_type', 'summary');
        
        // Client statistics
        $totalClients = PetOwner::count();
        $newClients = PetOwner::whereBetween('created_at', [$startDate, $endDate])->count();
        $activeClients = PetOwner::whereHas('pets.appointments', function($query) use ($startDate, $endDate) {
            $query->whereBetween('appointment_date', [$startDate, $endDate]);
        })->count();
        
        // Top clients by revenue
        $topClients = DB::table('pet_owners')
            ->join('users', 'pet_owners.user_id', '=', 'users.id')
            ->select(
                'pet_owners.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                DB::raw('SUM(billing_invoices.total_amount) as total_revenue')
            )
            ->join('billing_invoices', 'pet_owners.id', '=', 'billing_invoices.pet_owner_id')
            ->whereBetween('billing_invoices.invoice_date', [$startDate, $endDate])
            ->groupBy('pet_owners.id', 'users.first_name', 'users.last_name', 'users.email')
            ->orderBy('total_revenue', 'desc')
            ->limit(20)
            ->get();
        
        // Client acquisition trends
        $clientAcquisition = PetOwner::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Pets per client distribution
        $petsPerClient = DB::query()
            ->fromSub(function ($query) {
                $query->from('pet_owners')
                    ->leftJoin('pets', 'pet_owners.id', '=', 'pets.owner_id')
                    ->selectRaw('pet_owners.id as owner_id, COUNT(pets.id) as pet_count')
                    ->groupBy('pet_owners.id');
            }, 'owner_pets')
            ->selectRaw('pet_count, COUNT(*) as client_count')
            ->groupBy('pet_count')
            ->orderBy('pet_count')
            ->get();
        
        return view('admin.reports.client', compact(
            'startDate',
            'endDate',
            'reportType',
            'totalClients',
            'newClients',
            'activeClients',
            'topClients',
            'clientAcquisition',
            'petsPerClient'
        ));
    }
    
    /**
     * Generate appointment reports.
     */
    public function appointmentReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $reportType = $request->input('report_type', 'summary');
        
        // Appointment statistics
        $totalAppointments = Appointment::whereBetween('appointment_date', [$startDate, $endDate])->count();
        $completedAppointments = Appointment::whereBetween('appointment_date', [$startDate, $endDate])
            ->where('status', 'completed')->count();
        $cancelledAppointments = Appointment::whereBetween('appointment_date', [$startDate, $endDate])
            ->where('status', 'cancelled')->count();
        $noShowAppointments = Appointment::whereBetween('appointment_date', [$startDate, $endDate])
            ->where('status', 'no_show')->count();
        
        // Appointments by type
        $appointmentsByType = Appointment::select('type', DB::raw('COUNT(*) as count'))
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get();
        
        // Daily appointment trends
        $dailyTrends = Appointment::selectRaw('DATE(appointment_date) as date, COUNT(*) as count')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Peak hours
        $peakHours = Appointment::selectRaw('HOUR(start_time) as hour, COUNT(*) as count')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->get();
        
        return view('admin.reports.appointment', compact(
            'startDate',
            'endDate',
            'reportType',
            'totalAppointments',
            'completedAppointments',
            'cancelledAppointments',
            'noShowAppointments',
            'appointmentsByType',
            'dailyTrends',
            'peakHours'
        ));
    }
    
    /**
     * Export report to CSV.
     */
    public function exportReport(Request $request, $reportType)
    {
        $startDate = $request->input('start_date', now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        switch ($reportType) {
            case 'financial':
                $data = BillingInvoice::with(['pet', 'petOwner'])
                    ->whereBetween('invoice_date', [$startDate, $endDate])
                    ->get()
                    ->map(function ($invoice) {
                        return [
                            'Invoice Number' => $invoice->invoice_number,
                            'Date' => $invoice->invoice_date,
                            'Pet Owner' => $invoice->petOwner->name,
                            'Pet' => $invoice->pet->name,
                            'Total Amount' => $invoice->total_amount,
                            'Paid Amount' => $invoice->paid_amount,
                            'Balance' => $invoice->balance,
                            'Status' => $invoice->status,
                        ];
                    });
                $filename = 'financial_report_' . $startDate . '_to_' . $endDate . '.csv';
                break;
                
            case 'medical':
                $data = MedicalRecord::with(['pet', 'pet.petOwner'])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get()
                    ->map(function ($record) {
                        return [
                            'Date' => $record->created_at->format('Y-m-d'),
                            'Pet Owner' => $record->pet->petOwner->name,
                            'Pet' => $record->pet->name,
                            'Diagnosis' => $record->diagnosis,
                            'Treatment' => $record->treatment,
                            'Veterinarian' => $record->veterinarian_name ?? 'N/A',
                        ];
                    });
                $filename = 'medical_report_' . $startDate . '_to_' . $endDate . '.csv';
                break;
                
            default:
                return back()->withErrors(['error' => 'Invalid report type for export.']);
        }
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            if ($data->isNotEmpty()) {
                fputcsv($file, array_keys($data->first()));
                
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Generate report based on type.
     */
    private function generateReport($type)
    {
        switch ($type) {
            case 'financial':
                return $this->financialReport(request());
            case 'medical':
                return $this->medicalReport(request());
            case 'inventory':
                return $this->inventoryReport(request());
            case 'client':
                return $this->clientReport(request());
            case 'appointment':
                return $this->appointmentReport(request());
            default:
                return redirect()->route('admin.reports.index')
                    ->withErrors(['error' => 'Invalid report type.']);
        }
    }
}
