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
use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Dompdf\Dompdf;

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

        $reportsOverviewChart = $this->makeChart(
            'bar',
            ['Invoices', 'Appointments', 'Medical Records', 'Inventory Items'],
            [$totalInvoices, $totalAppointments, $totalMedicalRecords, $totalInventoryItems],
            'Total Records',
            [
                'rgba(37, 99, 235, 0.35)',
                'rgba(99, 102, 241, 0.35)',
                'rgba(34, 197, 94, 0.35)',
                'rgba(249, 115, 22, 0.35)',
            ],
            [
                'rgba(37, 99, 235, 1)',
                'rgba(99, 102, 241, 1)',
                'rgba(34, 197, 94, 1)',
                'rgba(249, 115, 22, 1)',
            ],
            [
                'legend' => ['display' => false],
            ]
        );

        return view('admin.reports.index', compact(
            'totalInvoices',
            'totalAppointments',
            'totalMedicalRecords',
            'totalInventoryItems',
            'reportsOverviewChart'
        ));
    }
    
    /**
     * Generate financial reports.
     */
    public function financialReport(Request $request)
    {
        $defaultStartDate = Invoice::query()->min('issue_date') ?? now()->toDateString();
        $startDate = $request->input('start_date', $defaultStartDate);
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

        $paidAmount = $invoices->sum(function ($invoice) {
            $invoiceTotal = (float) $invoice->total_amount;
            $invoicePaid = (float) $invoice->payments->sum('amount');

            return min($invoicePaid, $invoiceTotal);
        });

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

        $paymentMethods = collect();

        foreach ($invoices as $invoice) {
            $remaining = (float) $invoice->total_amount;

            foreach ($invoice->payments->sortBy('payment_date') as $payment) {
                if ($remaining <= 0) {
                    break;
                }

                $appliedAmount = min((float) $payment->amount, $remaining);

                if ($appliedAmount <= 0) {
                    continue;
                }

                $method = (string) ($payment->payment_method ?: 'unknown');

                if (! $paymentMethods->has($method)) {
                    $paymentMethods->put($method, (object) [
                        'payment_method' => $method,
                        'count' => 0,
                        'total' => 0,
                    ]);
                }

                $row = $paymentMethods->get($method);
                $row->count += 1;
                $row->total += $appliedAmount;
                $paymentMethods->put($method, $row);

                $remaining -= $appliedAmount;
            }
        }

        $paymentMethods = $paymentMethods
            ->values()
            ->sortByDesc('total')
            ->values();

        $revenueByMonthChart = $this->makeChart(
            'line',
            $revenueByMonth->map(function ($row) {
                return Carbon::createFromFormat('Y-m', $row->month)->format('M Y');
            })->all(),
            $revenueByMonth->map(function ($row) {
                return round((float) $row->revenue, 2);
            })->all(),
            'Revenue',
            'rgba(37, 99, 235, 0.15)',
            'rgba(37, 99, 235, 1)',
            [
                'legend' => ['display' => true],
            ]
        );

        $paymentMethodsChart = $this->makeChart(
            'doughnut',
            $paymentMethods->map(function ($row) {
                return ucfirst(str_replace('_', ' ', (string) data_get($row, 'payment_method')));
            })->all(),
            $paymentMethods->map(function ($row) {
                return round((float) $row->total, 2);
            })->all(),
            'Payment Totals',
            [
                'rgba(37, 99, 235, 0.75)',
                'rgba(249, 115, 22, 0.75)',
                'rgba(34, 197, 94, 0.75)',
                'rgba(236, 72, 153, 0.75)',
                'rgba(99, 102, 241, 0.75)',
            ],
            [
                'rgba(37, 99, 235, 1)',
                'rgba(249, 115, 22, 1)',
                'rgba(34, 197, 94, 1)',
                'rgba(236, 72, 153, 1)',
                'rgba(99, 102, 241, 1)',
            ]
        );

        $topServicesChart = $this->makeChart(
            'bar',
            $topServices->pluck('item_type')->map(function ($value) {
                return ucfirst(str_replace('_', ' ', (string) $value));
            })->all(),
            $topServices->pluck('total')->map(function ($value) {
                return round((float) $value, 2);
            })->all(),
            'Service Revenue',
            'rgba(99, 102, 241, 0.35)',
            'rgba(99, 102, 241, 1)',
            [
                'legend' => ['display' => false],
            ]
        );

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
            'outstandingInvoices',
            'revenueByMonthChart',
            'paymentMethodsChart',
            'topServicesChart'
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

        $appointmentStatusCounts = Appointment::whereBetween('appointment_date', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $appointmentStatuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'];
        $appointmentStatusLabels = ['Pending', 'Confirmed', 'In Progress', 'Completed', 'Cancelled', 'No Show'];
        $appointmentStatusData = collect($appointmentStatuses)
            ->map(function ($status) use ($appointmentStatusCounts) {
                return (int) ($appointmentStatusCounts[$status] ?? 0);
            })
            ->all();

        $medicalVolumeChart = $this->makeChart(
            'bar',
            ['Appointments', 'Medical Records', 'Prescriptions', 'Vaccinations', 'Surgeries'],
            [
                $totalAppointments,
                $totalMedicalRecords,
                $totalPrescriptions,
                $totalVaccinations,
                $totalSurgeries,
            ],
            'Medical Activity',
            [
                'rgba(37, 99, 235, 0.35)',
                'rgba(99, 102, 241, 0.35)',
                'rgba(236, 72, 153, 0.35)',
                'rgba(249, 115, 22, 0.35)',
                'rgba(20, 184, 166, 0.35)',
            ],
            [
                'rgba(37, 99, 235, 1)',
                'rgba(99, 102, 241, 1)',
                'rgba(236, 72, 153, 1)',
                'rgba(249, 115, 22, 1)',
                'rgba(20, 184, 166, 1)',
            ],
            [
                'legend' => ['display' => false],
            ]
        );

        $appointmentStatusChart = $this->makeChart(
            'bar',
            $appointmentStatusLabels,
            $appointmentStatusData,
            'Appointment Status',
            [
                'rgba(59, 130, 246, 0.35)',
                'rgba(16, 185, 129, 0.35)',
                'rgba(168, 85, 247, 0.35)',
                'rgba(34, 197, 94, 0.35)',
                'rgba(239, 68, 68, 0.35)',
                'rgba(245, 158, 11, 0.35)',
            ],
            [
                'rgba(59, 130, 246, 1)',
                'rgba(16, 185, 129, 1)',
                'rgba(168, 85, 247, 1)',
                'rgba(34, 197, 94, 1)',
                'rgba(239, 68, 68, 1)',
                'rgba(245, 158, 11, 1)',
            ],
            [
                'legend' => ['display' => false],
            ]
        );

        $commonDiagnosesChart = $this->makeChart(
            'bar',
            $commonDiagnoses->pluck('diagnosis')->map(function ($value) {
                return \Illuminate\Support\Str::limit($value, 28);
            })->all(),
            $commonDiagnoses->pluck('count')->all(),
            'Diagnoses',
            'rgba(249, 115, 22, 0.35)',
            'rgba(249, 115, 22, 1)',
            [
                'legend' => ['display' => false],
            ]
        );

        $petTypesChart = $this->makeChart(
            'pie',
            $petTypes->pluck('species')->map(function ($value) {
                return $value ?: 'Unknown';
            })->all(),
            $petTypes->pluck('count')->all(),
            'Species',
            [
                'rgba(37, 99, 235, 0.75)',
                'rgba(34, 197, 94, 0.75)',
                'rgba(249, 115, 22, 0.75)',
                'rgba(99, 102, 241, 0.75)',
                'rgba(236, 72, 153, 0.75)',
            ],
            [
                'rgba(37, 99, 235, 1)',
                'rgba(34, 197, 94, 1)',
                'rgba(249, 115, 22, 1)',
                'rgba(99, 102, 241, 1)',
                'rgba(236, 72, 153, 1)',
            ]
        );
        
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
            'petTypes',
            'medicalVolumeChart',
            'appointmentStatusChart',
            'commonDiagnosesChart',
            'petTypesChart'
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

        $healthyItems = max($totalItems - $lowStockItems - $expiredItems - $expiringSoonItems, 0);

        $inventoryStatusChart = $this->makeChart(
            'doughnut',
            ['Healthy', 'Low Stock', 'Expired', 'Expiring Soon'],
            [$healthyItems, $lowStockItems, $expiredItems, $expiringSoonItems],
            'Inventory Status',
            [
                'rgba(34, 197, 94, 0.75)',
                'rgba(249, 115, 22, 0.75)',
                'rgba(236, 72, 153, 0.75)',
                'rgba(59, 130, 246, 0.75)',
            ],
            [
                'rgba(34, 197, 94, 1)',
                'rgba(249, 115, 22, 1)',
                'rgba(236, 72, 153, 1)',
                'rgba(59, 130, 246, 1)',
            ]
        );

        $movementTrendsChart = new Chart();
        $movementTrendsChart->labels($movementTrends->pluck('date')->all());
        $movementTrendsChart
            ->dataset('Movements', 'line', $movementTrends->pluck('count')->map(fn ($value) => (int) $value)->all())
            ->color('rgba(37, 99, 235, 1)')
            ->backgroundColor('rgba(37, 99, 235, 0.15)')
            ->fill(false)
            ->lineTension(0.2);
        $movementTrendsChart
            ->dataset('Quantity', 'line', $movementTrends->pluck('total_quantity')->map(fn ($value) => (float) $value)->all())
            ->color('rgba(249, 115, 22, 1)')
            ->backgroundColor('rgba(249, 115, 22, 0.15)')
            ->fill(false)
            ->lineTension(0.2);
        $movementTrendsChart->options([
            'responsive' => true,
            'maintainAspectRatio' => false,
        ]);

        $topMovedItemsChart = $this->makeChart(
            'bar',
            $topMovedItems->pluck('name')->all(),
            $topMovedItems->pluck('total_quantity')->map(fn ($value) => (float) $value)->all(),
            'Moved Quantity',
            'rgba(99, 102, 241, 0.35)',
            'rgba(99, 102, 241, 1)',
            [
                'legend' => ['display' => false],
            ]
        );

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
            'topMovedItems',
            'inventoryStatusChart',
            'movementTrendsChart',
            'topMovedItemsChart'
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

        $clientStatsChart = $this->makeChart(
            'bar',
            ['Total Clients', 'New Clients', 'Active Clients'],
            [$totalClients, $newClients, $activeClients],
            'Clients',
            [
                'rgba(37, 99, 235, 0.35)',
                'rgba(34, 197, 94, 0.35)',
                'rgba(249, 115, 22, 0.35)',
            ],
            [
                'rgba(37, 99, 235, 1)',
                'rgba(34, 197, 94, 1)',
                'rgba(249, 115, 22, 1)',
            ],
            [
                'legend' => ['display' => false],
            ]
        );

        $clientAcquisitionChart = $this->makeChart(
            'line',
            $clientAcquisition->pluck('month')->all(),
            $clientAcquisition->pluck('count')->map(fn ($value) => (int) $value)->all(),
            'New Clients',
            'rgba(37, 99, 235, 0.15)',
            'rgba(37, 99, 235, 1)'
        );

        $petsPerClientChart = $this->makeChart(
            'bar',
            $petsPerClient->pluck('pet_count')->map(fn ($value) => (string) $value)->all(),
            $petsPerClient->pluck('client_count')->map(fn ($value) => (int) $value)->all(),
            'Clients',
            'rgba(99, 102, 241, 0.35)',
            'rgba(99, 102, 241, 1)',
            [
                'legend' => ['display' => false],
            ]
        );
        
        return view('admin.reports.client', compact(
            'startDate',
            'endDate',
            'reportType',
            'totalClients',
            'newClients',
            'activeClients',
            'topClients',
            'clientAcquisition',
            'petsPerClient',
            'clientStatsChart',
            'clientAcquisitionChart',
            'petsPerClientChart'
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

        $otherAppointments = max($totalAppointments - ($completedAppointments + $cancelledAppointments + $noShowAppointments), 0);

        $appointmentStatusChart = $this->makeChart(
            'doughnut',
            ['Completed', 'Cancelled', 'No Show', 'Other'],
            [$completedAppointments, $cancelledAppointments, $noShowAppointments, $otherAppointments],
            'Appointment Status',
            [
                'rgba(34, 197, 94, 0.75)',
                'rgba(249, 115, 22, 0.75)',
                'rgba(236, 72, 153, 0.75)',
                'rgba(59, 130, 246, 0.75)',
            ],
            [
                'rgba(34, 197, 94, 1)',
                'rgba(249, 115, 22, 1)',
                'rgba(236, 72, 153, 1)',
                'rgba(59, 130, 246, 1)',
            ]
        );

        $appointmentsByTypeChart = $this->makeChart(
            'bar',
            $appointmentsByType->pluck('type')->map(fn ($value) => ucfirst(str_replace('_', ' ', $value)))->all(),
            $appointmentsByType->pluck('count')->map(fn ($value) => (int) $value)->all(),
            'Appointments',
            'rgba(99, 102, 241, 0.35)',
            'rgba(99, 102, 241, 1)',
            [
                'legend' => ['display' => false],
            ]
        );

        $dailyTrendsChart = $this->makeChart(
            'line',
            $dailyTrends->pluck('date')->all(),
            $dailyTrends->pluck('count')->map(fn ($value) => (int) $value)->all(),
            'Appointments per Day',
            'rgba(37, 99, 235, 0.15)',
            'rgba(37, 99, 235, 1)'
        );

        $peakHoursChart = $this->makeChart(
            'bar',
            $peakHours->pluck('hour')->map(fn ($value) => sprintf('%02d:00', (int) $value))->all(),
            $peakHours->pluck('count')->map(fn ($value) => (int) $value)->all(),
            'Peak Hours',
            'rgba(249, 115, 22, 0.35)',
            'rgba(249, 115, 22, 1)',
            [
                'legend' => ['display' => false],
            ]
        );
        
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
            'peakHours',
            'appointmentStatusChart',
            'appointmentsByTypeChart',
            'dailyTrendsChart',
            'peakHoursChart'
        ));
    }

    private function makeChart(
        string $type,
        array $labels,
        array $data,
        string $datasetLabel,
        array|string $backgroundColor,
        array|string|null $borderColor = null,
        array $options = []
    ): Chart {
        $chart = new Chart();
        $chart->labels($labels);

        $dataset = $chart
            ->dataset($datasetLabel, $type, $data)
            ->backgroundColor($backgroundColor);

        if ($borderColor !== null) {
            $dataset->color($borderColor);
        }

        if ($type === 'line') {
            $dataset->fill(false)->lineTension(0.2);
        }

        $chart->options(array_replace_recursive([
            'responsive' => true,
            'maintainAspectRatio' => false,
        ], $options));

        return $chart;
    }
    
    /**
     * Export report to CSV.
     */
    public function exportReport(Request $request, $reportType)
    {
        $startDate = $request->input('start_date', now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $format = $request->input('format', 'csv');
        
        $data = [];
        $filename = '';
        $viewName = '';
        
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
                $filename = 'financial_report_' . $startDate . '_to_' . $endDate;
                $viewName = 'admin.reports.financial-pdf';
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
                $filename = 'medical_report_' . $startDate . '_to_' . $endDate;
                $viewName = 'admin.reports.medical-pdf';
                break;
                
            case 'inventory':
                $data = InventoryItem::with(['inventoryStocks'])
                    ->get()
                    ->map(function ($item) {
                        return [
                            'Item Name' => $item->name,
                            'Category' => $item->category,
                            'Current Stock' => $item->inventoryStocks->sum('quantity'),
                            'Unit' => $item->unit,
                            'Supplier' => $item->supplier,
                            'Last Updated' => $item->updated_at->format('Y-m-d'),
                        ];
                    });
                $filename = 'inventory_report_' . date('Y-m-d');
                $viewName = 'admin.reports.inventory-pdf';
                break;
                
            case 'client':
                $data = PetOwner::with(['user', 'pets', 'invoices'])
                    ->get()
                    ->map(function ($owner) {
                        return [
                            'Name' => $owner->full_name,
                            'Email' => $owner->user?->email ?? $owner->email,
                            'Phone' => $owner->user?->phone ?? $owner->phone,
                            'Address' => $owner->user?->address ?? $owner->address,
                            'Pets Count' => $owner->pets->count(),
                            'Total Invoices' => $owner->invoices->count(),
                        ];
                    });
                $filename = 'client_report_' . date('Y-m-d');
                $viewName = 'admin.reports.client-pdf';
                break;
                
            case 'appointment':
                $data = Appointment::with(['pet.owner', 'veterinarian'])
                    ->whereBetween('appointment_date', [$startDate, $endDate])
                    ->get()
                    ->map(function ($appointment) {
                        return [
                            'Date' => $appointment->appointment_date->format('Y-m-d'),
                            'Time' => $appointment->appointment_time,
                            'Pet Owner' => $appointment->pet?->owner?->full_name ?? '',
                            'Pet' => $appointment->pet?->name ?? '',
                            'Veterinarian' => $appointment->veterinarian?->full_name ?? '',
                            'Type' => $appointment->appointment_type ?? $appointment->type,
                            'Status' => $appointment->status,
                        ];
                    });
                $filename = 'appointment_report_' . $startDate . '_to_' . $endDate;
                $viewName = 'admin.reports.appointment-pdf';
                break;
                
            default:
                return back()->withErrors(['error' => 'Invalid report type for export.']);
        }
        
        if ($format === 'pdf') {
            // Generate PDF
            $html = view($viewName, compact('data', 'startDate', 'endDate', 'reportType'))->render();
            
            $options = new \Dompdf\Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            $fullFilename = $filename . '.pdf';
            
            return response($dompdf->output())
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "attachment; filename=\"{$fullFilename}\"")
                ->header('Content-Length', strlen($dompdf->output()));
        } else {
            // Default to CSV
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
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
