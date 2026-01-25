<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\PetOwner;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $hasPets = Schema::hasTable('pets');
        $hasPetOwners = Schema::hasTable('pet_owners');
        $hasAppointments = Schema::hasTable('appointments');
        $hasVaccinations = Schema::hasTable('pet_vaccinations');
        $hasVaccines = Schema::hasTable('vaccines');
        $hasCages = Schema::hasTable('cages');
        $hasInventoryStock = Schema::hasTable('inventory_stock');
        $hasInventoryItems = Schema::hasTable('inventory_items');
        $hasSuppliers = Schema::hasTable('suppliers');

        $petCount = $hasPets ? Pet::count() : 0;
        $petOwnerCount = $hasPetOwners ? PetOwner::count() : 0;

        $activeAppointmentsCount = $hasAppointments
            ? DB::table('appointments')
                ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                ->count()
            : 0;

        $appointmentsToday = $hasAppointments
            ? DB::table('appointments')
                ->whereDate('appointment_date', Carbon::today())
                ->count()
            : 0;

        $speciesBreakdown = $hasPets
            ? Pet::select('species', DB::raw('COUNT(*) as total'))
                ->groupBy('species')
                ->orderByDesc('total')
                ->get()
                ->map(function ($row) {
                    $row->label = $row->species ? ucfirst($row->species) : 'Unknown';
                    return $row;
                })
            : collect();

        $palette = [
            '#FF8C42', '#4A90E2', '#FF6B9D', '#9B7EDE', '#5FD068',
            '#FFD166', '#73C2FB', '#F78DA7', '#8E44AD', '#2ECC71'
        ];

        if ($speciesBreakdown->isEmpty()) {
            $speciesChart = [
                'labels' => ['No Data'],
                'counts' => [0],
                'colors' => [$palette[0]],
                'hasData' => false,
            ];
        } else {
            $speciesChart = [
                'labels' => $speciesBreakdown->pluck('label')->values()->toArray(),
                'counts' => $speciesBreakdown->pluck('total')->values()->toArray(),
                'colors' => $speciesBreakdown->values()->map(function ($row, $index) use ($palette) {
                    return $palette[$index % count($palette)];
                })->toArray(),
                'hasData' => true,
            ];
        }

        $petAges = $hasPets
            ? Pet::whereNotNull('birth_date')->get()->map(function ($pet) {
                return Carbon::parse($pet->birth_date)->age;
            })
            : collect();

        $petAgeStats = [
            'average' => $petAges->isNotEmpty() ? round($petAges->avg(), 1) : null,
            'youngest' => $petAges->isNotEmpty() ? $petAges->min() : null,
            'oldest' => $petAges->isNotEmpty() ? $petAges->max() : null,
        ];

        $upcomingAppointments = $hasAppointments
            ? DB::table('appointments')
                ->leftJoin('pets', 'appointments.pet_id', '=', 'pets.id')
                ->leftJoin('pet_owners', 'pets.owner_id', '=', 'pet_owners.id')
                ->leftJoin('users as owners', 'pet_owners.user_id', '=', 'owners.id')
                ->leftJoin('users as vets', 'appointments.veterinarian_id', '=', 'vets.id')
                ->where('appointments.appointment_date', '>=', Carbon::now()->startOfDay())
                ->orderBy('appointments.appointment_date')
                ->limit(5)
                ->get([
                    'appointments.id',
                    'appointments.appointment_date',
                    'appointments.type',
                    'appointments.status',
                    'pets.name as pet_name',
                    DB::raw("owners.first_name || ' ' || owners.last_name as owner_name"),
                    DB::raw("vets.first_name || ' ' || vets.last_name as veterinarian_name"),
                ])
                ->map(function ($appointment) {
                    $appointment->formatted_date = $appointment->appointment_date
                        ? Carbon::parse($appointment->appointment_date)->format('M d, Y g:i A')
                        : 'TBD';
                    $appointment->status_label = $appointment->status
                        ? ucfirst(str_replace('_', ' ', $appointment->status))
                        : 'Unknown';
                    $appointment->type_label = $appointment->type
                        ? ucfirst(str_replace('_', ' ', $appointment->type))
                        : 'General';

                    return $appointment;
                })
            : collect();

        $vaccinationsDueSoon = collect();
        $vaccinationsDueSoonCount = 0;
        if ($hasVaccinations && $hasPets && $hasVaccines) {
            $vaccinationsDueSoonQuery = DB::table('pet_vaccinations')
                ->leftJoin('pets', 'pet_vaccinations.pet_id', '=', 'pets.id')
                ->leftJoin('vaccines', 'pet_vaccinations.vaccine_id', '=', 'vaccines.id')
                ->leftJoin('users as admins', 'pet_vaccinations.administered_by', '=', 'admins.id')
                ->whereBetween('pet_vaccinations.next_due_date', [Carbon::today(), Carbon::today()->copy()->addDays(30)]);

            $vaccinationsDueSoonCount = $vaccinationsDueSoonQuery->count();

            $vaccinationsDueSoon = $vaccinationsDueSoonQuery
                ->orderBy('pet_vaccinations.next_due_date')
                ->limit(5)
                ->get([
                    'pet_vaccinations.next_due_date',
                    'pet_vaccinations.notes',
                    'pet_vaccinations.dose_number',
                    'pets.name as pet_name',
                    'vaccines.vaccine_name',
                    DB::raw("CONCAT(admins.first_name, ' ', admins.last_name) as administered_by"),
                ])
                ->map(function ($vaccination) {
                    $vaccination->formatted_due_date = $vaccination->next_due_date
                        ? Carbon::parse($vaccination->next_due_date)->format('M d, Y')
                        : 'TBD';

                    return $vaccination;
                });
        }

        $appointmentStatusSummary = $hasAppointments
            ? DB::table('appointments')
                ->select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->get()
                ->map(function ($row) {
                    $row->label = $row->status ? ucfirst(str_replace('_', ' ', $row->status)) : 'Unspecified';
                    return $row;
                })
            : collect();

        if ($appointmentStatusSummary->isEmpty()) {
            $appointmentStatusChart = [
                'labels' => ['No Data'],
                'counts' => [0],
                'colors' => [$palette[3]],
                'hasData' => false,
            ];
        } else {
            $appointmentStatusChart = [
                'labels' => $appointmentStatusSummary->pluck('label')->values()->toArray(),
                'counts' => $appointmentStatusSummary->pluck('total')->values()->toArray(),
                'colors' => $appointmentStatusSummary->values()->map(function ($row, $index) use ($palette) {
                    return $palette[($index + 3) % count($palette)];
                })->toArray(),
                'hasData' => true,
            ];
        }

        $boardingCapacity = $hasCages ? DB::table('cages')->count() : 0;
        $occupiedCages = $hasCages ? DB::table('cages')->where('status', 'occupied')->count() : 0;
        $boardingOccupancy = $boardingCapacity > 0
            ? round(($occupiedCages / $boardingCapacity) * 100)
            : 0;

        $lowStockItems = collect();
        $lowStockCount = 0;
        if ($hasInventoryStock && $hasInventoryItems) {
            $lowStockCount = DB::table('inventory_stock')
                ->whereColumn('quantity', '<=', 'min_stock')
                ->count();

            $lowStockItems = DB::table('inventory_stock')
                ->join('inventory_items', 'inventory_stock.item_id', '=', 'inventory_items.id')
                ->when($hasSuppliers, function ($query) {
                    return $query->leftJoin('suppliers', 'inventory_stock.supplier_id', '=', 'suppliers.id');
                })
                ->whereColumn('inventory_stock.quantity', '<=', 'inventory_stock.min_stock')
                ->orderBy('inventory_stock.quantity')
                ->limit(5)
                ->get([
                    'inventory_items.name as item_name',
                    'inventory_items.category',
                    'inventory_stock.quantity',
                    'inventory_stock.min_stock',
                    'inventory_stock.expiry_date',
                    $hasSuppliers ? 'suppliers.supplier_name' : DB::raw('NULL as supplier_name'),
                ])
                ->map(function ($item) {
                    $item->status_badge = $item->quantity <= 0 ? 'Out of Stock' : 'Low Stock';
                    $item->expiry_label = $item->expiry_date
                        ? Carbon::parse($item->expiry_date)->format('M d, Y')
                        : 'N/A';

                    return $item;
                });
        }

        $recentPets = $hasPets
            ? Pet::with(['owner.user'])
                ->orderByDesc('id')
                ->limit(5)
                ->get()
                ->map(function ($pet) {
                    return [
                        'name' => $pet->name,
                        'species' => $pet->species ? ucfirst($pet->species) : 'Unknown',
                        'breed' => $pet->breed,
                        'owner' => optional($pet->owner?->user)->first_name && optional($pet->owner?->user)->last_name
                            ? $pet->owner->user->first_name . ' ' . $pet->owner->user->last_name
                            : 'N/A',
                        'gender' => $pet->gender ? ucfirst($pet->gender) : 'Unknown',
                    ];
                })
            : collect();

        return view('admin.dashboard.home', [
            'petCount' => $petCount,
            'petOwnerCount' => $petOwnerCount,
            'activeAppointmentsCount' => $activeAppointmentsCount,
            'appointmentsToday' => $appointmentsToday,
            'speciesChart' => $speciesChart,
            'petAgeStats' => $petAgeStats,
            'upcomingAppointments' => $upcomingAppointments,
            'appointmentStatusChart' => $appointmentStatusChart,
            'boardingCapacity' => $boardingCapacity,
            'occupiedCages' => $occupiedCages,
            'boardingOccupancy' => $boardingOccupancy,
            'vaccinationsDueSoon' => $vaccinationsDueSoon,
            'vaccinationsDueSoonCount' => $vaccinationsDueSoonCount,
            'lowStockItems' => $lowStockItems,
            'lowStockCount' => $lowStockCount,
            'recentPets' => $recentPets,
        ]);
    }
}
