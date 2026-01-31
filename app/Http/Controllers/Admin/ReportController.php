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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends BaseController
{
    /**
     * Display a listing of reports.
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Show the form for creating a new report.
     */
    public function create()
    {
        return view('admin.reports.create');
    }

    /**
     * Store a newly created report in storage.
     */
    public function store(Request $request)
    {
        // Reports are generated, not stored
        return redirect()->route('admin.reports.index');
    }

    /**
     * Display the specified report.
     */
    public function show($id)
    {
        return $this->generateReport($id);
    }

    /**
     * Show the form for editing the specified report.
     */
    public function edit($id)
    {
        return redirect()->route('admin.reports.index');
    }

    /**
     * Update the specified report in storage.
     */
    public function update(Request $request, $id)
    {
        return redirect()->route('admin.reports.index');
    }

    /**
     * Remove the specified report from storage.
     */
    public function destroy($id)
    {
        return redirect()->route('admin.reports.index');
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
        $totalMedicalRecords = MedicalRecord::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // Prescriptions
        $totalPrescriptions = Prescription::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // Vaccinations
        $totalVaccinations = Vaccination::whereBetween('vaccination_date', [$startDate, $endDate])->count();
        
        // Surgeries
        $totalSurgeries = Surgery::whereBetween('surgery_date', [$startDate, $endDate])->count();
        
        // Common diagnoses (from medical records)
        $commonDiagnoses = MedicalRecord::select('diagnosis', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('diagnosis')
            ->groupBy('diagnosis')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        
        // Common treatments
        $commonTreatments = MedicalRecord::select('treatment', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('treatment')
            ->groupBy('treatment')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        
        // Pet types treated
        $petTypes = Pet::select('species', DB::raw('COUNT(*) as count'))
            ->join('medical_records', 'pets.id', '=', 'medical_records.pet_id')
            ->whereBetween('medical_records.created_at', [$startDate, $endDate])
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
            ->select('pet_owners.*', DB::raw('SUM(billing_invoices.total_amount) as total_revenue'))
            ->join('billing_invoices', 'pet_owners.id', '=', 'billing_invoices.pet_owner_id')
            ->whereBetween('billing_invoices.invoice_date', [$startDate, $endDate])
            ->groupBy('pet_owners.id')
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
        $petsPerClient = DB::table('pet_owners')
            ->selectRaw('COUNT(pets.id) as pet_count, COUNT(*) as client_count')
            ->leftJoin('pets', 'pet_owners.id', '=', 'pets.pet_owner_id')
            ->groupBy('pet_owners.id')
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
        $appointmentsByType = Appointment::select('appointment_type', DB::raw('COUNT(*) as count'))
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->groupBy('appointment_type')
            ->orderBy('count', 'desc')
            ->get();
        
        // Daily appointment trends
        $dailyTrends = Appointment::selectRaw('DATE(appointment_date) as date, COUNT(*) as count')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Peak hours
        $peakHours = Appointment::selectRaw('HOUR(appointment_time) as hour, COUNT(*) as count')
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
