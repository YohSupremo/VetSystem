<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\BillingInvoice;
use App\Models\BillingPayment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\PetOwner;
use App\Models\InventoryItem;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\PetVaccination;
use App\Models\Surgery;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;

class ReportController extends BaseController
{
    /**
     * Display a listing of reports.
     */
    public function index()
    {
        $totalInvoices = BillingInvoice::where('status', '!=', 'cancelled')->count();
        $totalAppointments = Appointment::count();
        $totalMedicalRecords = MedicalRecord::count();
        $totalInventoryItems = InventoryItem::count();

        return view('admin.reports.index', compact(
            'totalInvoices',
            'totalAppointments',
            'totalMedicalRecords',
            'totalInventoryItems'
        ));
    }
    
    /**
     * Generate financial reports.
     */
    public function financialReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $reportType = $request->input('report_type', 'summary');
        
        $invoices = Invoice::with(['invoiceItems', 'payments'])
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->get();
        $totalInvoices = $invoices->count();
        $totalRevenue = $invoices->sum(function ($invoice) {
            return (float) $invoice->total_amount;
        });

        $paidAmount = Payment::query()
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->whereDate('payments.payment_date', '>=', $startDate)
            ->whereDate('payments.payment_date', '<=', $endDate)
            ->where('invoices.status', '!=', 'cancelled')
            ->sum('payments.amount');

        $outstandingAmount = $invoices->sum(function ($invoice) {
            return (float) $invoice->balance;
        });

        $revenueByMonth = $invoices
            ->groupBy(function ($inv) { return $inv->issue_date->format('Y-m'); })
            ->map(function ($group) {
                return (object)[
                    'month' => $group->first()->issue_date->format('Y-m'),
                    'revenue' => $group->sum(function ($invoice) {
                        return (float) $invoice->total_amount;
                    })
                ];
            })->values();

        $topServices = DB::table('invoice_items')
            ->select('item_type', DB::raw('COUNT(*) as count, SUM(quantity * unit_price) as total'))
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->whereBetween('invoices.issue_date', [$startDate, $endDate])
            ->where('invoices.status', '!=', 'cancelled')
            ->groupBy('item_type')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $paymentMethods = DB::table('payments')
            ->select('payment_method', DB::raw('COUNT(*) as count, SUM(amount) as total'))
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->whereDate('payments.payment_date', '>=', $startDate)
            ->whereDate('payments.payment_date', '<=', $endDate)
            ->where('invoices.status', '!=', 'cancelled')
            ->groupBy('payment_method')
            ->get();

        $outstandingInvoices = Invoice::with(['pet', 'petOwner'])
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->orderBy('due_date', 'asc')
            ->limit(20)
            ->get();

        // Get cancelled invoices separately
        $cancelledInvoices = Invoice::with(['invoiceItems', 'payments'])
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->where('status', 'cancelled')
            ->get();
        $cancelledCount = $cancelledInvoices->count();
        $cancelledAmount = $cancelledInvoices->sum(function ($invoice) {
            return (float) $invoice->total_amount;
        });
        
        return view('admin.reports.financial', compact(
            'startDate',
            'endDate',
            'reportType',
            'totalInvoices',
            'totalRevenue',
            'paidAmount',
            'outstandingAmount',
            'cancelledCount',
            'cancelledAmount',
            'revenueByMonth',
            'topServices',
            'paymentMethods',
            'outstandingInvoices'
        ));
    }

    /**
     * Display all cancelled invoices with filters.
     */
    public function cancelledInvoices(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $sortBy = $request->input('sort_by', 'issue_date');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = BillingInvoice::with(['pet', 'petOwner', 'invoiceItems', 'payments', 'order'])
            ->where('status', 'cancelled')
            ->whereBetween('issue_date', [$startDate, $endDate]);

        // Apply sorting (only sort by columns that exist in invoices table)
        $validSortColumns = ['issue_date', 'invoice_number'];
        if (in_array($sortBy, $validSortColumns)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('issue_date', 'desc');
        }

        $invoices = $query->paginate(20);
        
        // Get all cancelled invoices for summary (with items to calculate total)
        $allCancelledInvoices = BillingInvoice::with('invoiceItems')
            ->where('status', 'cancelled')
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->get();
        
        $totalCancelled = $allCancelledInvoices->count();
        $totalCancelledAmount = $allCancelledInvoices->sum(function ($invoice) {
            return $invoice->subtotal + ($invoice->subtotal * ($invoice->tax_rate / 100)) - $invoice->discount_amount;
        });

        return view('admin.reports.cancelled-invoices', compact(
            'invoices',
            'startDate',
            'endDate',
            'sortBy',
            'sortOrder',
            'totalCancelled',
            'totalCancelledAmount'
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
        $totalVaccinations = PetVaccination::whereBetween('administered_date', [$startDate, $endDate])->count();
        
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
        $startDate = $request->input('start_date', now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $reportType = $request->input('report_type', 'summary');

        // Current inventory status
        $totalItems = InventoryItem::count();
        $lowStockItems = InventoryStock::whereColumn('quantity', '<=', 'min_stock')->count();
        $expiredItems = InventoryStock::whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->count();
        $expiringSoonItems = InventoryStock::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays(30)])
            ->count();

        // Inventory value
        $totalValue = InventoryStock::join('inventory_items', 'inventory_stock.item_id', '=', 'inventory_items.id')
            ->selectRaw('SUM(inventory_stock.quantity * inventory_items.unit_price) as total_value')
            ->value('total_value') ?? 0;

        // Low stock items
        $lowStockList = InventoryStock::with('inventoryItem')
            ->whereColumn('quantity', '<=', 'min_stock')
            ->orderBy('quantity')
            ->limit(20)
            ->get();

        // Expired items
        $expiredList = InventoryStock::with('inventoryItem')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->orderBy('expiry_date')
            ->limit(20)
            ->get();

        // Expiring soon items
        $expiringSoonList = InventoryStock::with('inventoryItem')
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays(30)])
            ->orderBy('expiry_date')
            ->limit(20)
            ->get();

        // Stock movement trends
        $movementTrends = InventoryTransaction::selectRaw('DATE(transaction_date) as date, COUNT(*) as count, SUM(ABS(quantity)) as total_quantity')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top moved items
        $topMovedItems = InventoryTransaction::join('inventory_stock', 'inventory_transactions.stock_id', '=', 'inventory_stock.id')
            ->join('inventory_items', 'inventory_stock.item_id', '=', 'inventory_items.id')
            ->whereBetween('inventory_transactions.transaction_date', [$startDate, $endDate])
            ->select(
                'inventory_items.name',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(ABS(inventory_transactions.quantity)) as total_quantity')
            )
            ->groupBy('inventory_items.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return view('admin.reports.inventory', compact(
            'startDate',
            'endDate',
            'reportType',
            'totalItems',
            'lowStockItems',
            'expiredItems',
            'expiringSoonItems',
            'totalValue',
            'lowStockList',
            'expiredList',
            'expiringSoonList',
            'movementTrends',
            'topMovedItems'
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
            ->join('invoices', 'pet_owners.id', '=', 'invoices.owner_id')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->select(
                'pet_owners.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                DB::raw('SUM(invoice_items.quantity * invoice_items.unit_price) as total_revenue')
            )
            ->whereBetween('invoices.issue_date', [$startDate, $endDate])
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
        $peakHours = Appointment::selectRaw('HOUR(appointment_date) as hour, COUNT(*) as count')
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
                    ->whereBetween('issue_date', [$startDate, $endDate])
                    ->get()
                    ->map(function ($invoice) {
                        return [
                            'Invoice Number' => $invoice->invoice_number,
                            'Date' => $invoice->issue_date?->format('Y-m-d'),
                            'Pet Owner' => $invoice->petOwner?->full_name ?? '',
                            'Pet' => $invoice->pet?->name ?? '',
                            'Total Amount' => $invoice->total_amount,
                            'Paid Amount' => $invoice->paid_amount,
                            'Balance' => $invoice->balance,
                            'Status' => $invoice->status,
                        ];
                    });
                $filename = 'financial_report_' . $startDate . '_to_' . $endDate . '.csv';
                break;
                
            case 'medical':
                $data = MedicalRecord::with(['pet', 'pet.owner'])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get()
                    ->map(function ($record) {
                        return [
                            'Date' => $record->created_at->format('Y-m-d'),
                            'Pet Owner' => $record->pet?->owner?->name ?? '',
                            'Pet' => $record->pet?->name ?? '',
                            'Diagnosis' => $record->diagnosis,
                            'Treatment' => $record->treatment_plan ?? '',
                            'Veterinarian' => $record->veterinarian ? $record->veterinarian->full_name : 'N/A',
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
