<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Order;
use App\Models\Pet;
use App\Models\PetOwner;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $hasCages = Schema::hasTable('cages');
        $hasInventoryStock = Schema::hasTable('inventory_stock');
        $hasInventoryItems = Schema::hasTable('inventory_items');
        $hasSuppliers = Schema::hasTable('suppliers');

        $petCount = $hasPets ? Pet::count() : 0;
        $petOwnerCount = $hasPetOwners ? PetOwner::count() : 0;

        // Check if user is a veterinarian for filtering
        $user = auth()->user();
        $isVeterinarian = $user && $user->isVeterinarian();
        $veterinarianId = $isVeterinarian ? $user->id : null;

        $activeAppointmentsCount = $hasAppointments
            ? ($isVeterinarian 
                ? DB::table('appointments')
                    ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                    ->where('veterinarian_id', $veterinarianId)
                    ->count()
                : DB::table('appointments')
                    ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                    ->count())
            : 0;

        $appointmentsToday = $hasAppointments
            ? ($isVeterinarian
                ? DB::table('appointments')
                    ->whereDate('appointment_date', Carbon::today())
                    ->where('veterinarian_id', $veterinarianId)
                    ->count()
                : DB::table('appointments')
                    ->whereDate('appointment_date', Carbon::today())
                    ->count())
            : 0;

        $speciesOptions = [
            'dog' => 'Dog',
            'cat' => 'Cat',
        ];

        $speciesCounts = array_fill_keys(array_keys($speciesOptions), 0);
        $speciesPets = array_fill_keys(array_keys($speciesOptions), []);
        if ($hasPets) {
            $speciesBreakdown = Pet::selectRaw("LOWER(TRIM(COALESCE(NULLIF(species, ''), ''))) as species_key, COUNT(*) as total")
                ->groupBy('species_key')
                ->orderByDesc('total')
                ->get();

            foreach ($speciesBreakdown as $row) {
                $normalizedKey = str_replace(' ', '_', (string) $row->species_key);
                if (array_key_exists($normalizedKey, $speciesCounts)) {
                    $speciesCounts[$normalizedKey] += (int) $row->total;
                }
            }

            $petsBySpecies = Pet::select('name', 'species')
                ->orderBy('name')
                ->get();

            foreach ($petsBySpecies as $pet) {
                $rawSpecies = strtolower(trim((string) $pet->species));
                $normalizedKey = str_replace(' ', '_', $rawSpecies);
                if (array_key_exists($normalizedKey, $speciesPets)) {
                    $speciesPets[$normalizedKey][] = $pet->name ?: 'Unnamed Pet';
                }
            }
        }

        $palette = [
            '#FF8C42', '#4A90E2', '#FF6B9D', '#9B7EDE', '#5FD068',
            '#FFD166', '#73C2FB', '#F78DA7', '#8E44AD', '#2ECC71'
        ];

        $totalSpeciesCount = array_sum($speciesCounts);
        $speciesChart = [
            'labels' => array_values($speciesOptions),
            'counts' => array_values($speciesCounts),
            'colors' => array_values($speciesCounts),
            'hasData' => $totalSpeciesCount > 0,
        ];

        $speciesChart['colors'] = array_map(function ($index) use ($palette) {
            return $palette[$index % count($palette)];
        }, array_keys($speciesChart['counts']));

        $petAges = collect();
        if ($hasPets) {
            $petAges = Pet::whereNotNull('birth_date')
                ->whereDate('birth_date', '<=', Carbon::today())
                ->get()
                ->map(function ($pet) {
                    try {
                        return Carbon::parse($pet->birth_date)->age;
                    } catch (\Exception $exception) {
                        return null;
                    }
                })
                ->filter(function ($age) {
                    return $age !== null;
                })
                ->values();
        }

        $petAgeStats = [
            'average' => $petAges->isNotEmpty() ? round($petAges->avg(), 1) : null,
            'youngest' => $petAges->isNotEmpty() ? $petAges->min() : null,
            'oldest' => $petAges->isNotEmpty() ? $petAges->max() : null,
            'count' => $petAges->count(),
        ];

        $upcomingAppointments = $hasAppointments
            ? ($isVeterinarian
                ? DB::table('appointments')
                    ->leftJoin('pets', 'appointments.pet_id', '=', 'pets.id')
                    ->leftJoin('pet_owners', 'pets.owner_id', '=', 'pet_owners.id')
                    ->leftJoin('users as owners', 'pet_owners.user_id', '=', 'owners.id')
                    ->leftJoin('users as vets', 'appointments.veterinarian_id', '=', 'vets.id')
                    ->where('appointments.appointment_date', '>=', Carbon::now()->startOfDay())
                    ->where('appointments.veterinarian_id', $veterinarianId)
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
                : DB::table('appointments')
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
                    ]))
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
        if ($hasVaccinations && $hasPets && $hasInventoryItems) {
            $vaccinationsDueSoonQuery = DB::table('pet_vaccinations')
                ->leftJoin('pets', 'pet_vaccinations.pet_id', '=', 'pets.id')
                ->leftJoin('inventory_items', 'pet_vaccinations.inventory_item_id', '=', 'inventory_items.id')
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
                    'inventory_items.name as vaccine_name',
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
            : collect();

        $statusOrder = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'];
        $statusLabels = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'no_show' => 'No Show',
        ];

        $statusCounts = array_fill_keys($statusOrder, 0);
        foreach ($appointmentStatusSummary as $row) {
            $statusKey = $row->status ?: 'unspecified';
            if (!array_key_exists($statusKey, $statusCounts)) {
                $statusCounts[$statusKey] = 0;
                $statusOrder[] = $statusKey;
                $statusLabels[$statusKey] = ucfirst(str_replace('_', ' ', $statusKey));
            }
            $statusCounts[$statusKey] = (int) $row->total;
        }

        $totalAppointments = array_sum($statusCounts);
        $appointmentStatusChart = [
            'labels' => array_map(function ($statusKey) use ($statusLabels) {
                return $statusLabels[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));
            }, $statusOrder),
            'counts' => array_map(function ($statusKey) use ($statusCounts) {
                return $statusCounts[$statusKey] ?? 0;
            }, $statusOrder),
            'colors' => array_map(function ($index) use ($palette) {
                return $palette[($index + 3) % count($palette)];
            }, array_keys($statusOrder)),
            'hasData' => $totalAppointments > 0,
        ];

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
            'speciesPets' => $speciesPets,
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

    public function globalSearch(Request $request)
    {
        $term = trim((string) $request->query('q', ''));

        if ($term === '' || strlen($term) < 2) {
            return response()->json(['results' => []]);
        }

        $like = '%' . $term . '%';
        $results = collect();

        $pets = Pet::with('owner.user')
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('species', 'like', $like)
                    ->orWhere('breed', 'like', $like)
                    ->orWhere('registration_number', 'like', $like)
                    ->orWhereHas('owner.user', function ($ownerUser) use ($like) {
                        $ownerUser->where('first_name', 'like', $like)
                            ->orWhere('last_name', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    });
            })
            ->limit(5)
            ->get();

        foreach ($pets as $pet) {
            $ownerName = trim((string) ($pet->owner?->user?->first_name . ' ' . $pet->owner?->user?->last_name));
            $results->push([
                'type' => 'Pet',
                'icon' => 'fa-paw',
                'title' => (string) $pet->name,
                'subtitle' => trim((string) (($pet->species ? ucfirst($pet->species) : 'Unknown') . ' - Owner: ' . ($ownerName !== '' ? $ownerName : 'N/A'))),
                'category' => 'Pets Registry',
                'url' => route('admin.pets.show', $pet->id),
            ]);
        }

        $owners = PetOwner::with('user')
            ->whereHas('user', function ($query) use ($like) {
                $query->where('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('username', 'like', $like);
            })
            ->limit(5)
            ->get();

        foreach ($owners as $owner) {
            $fullName = trim((string) (($owner->user?->first_name ?? '') . ' ' . ($owner->user?->last_name ?? '')));
            $results->push([
                'type' => 'Owner',
                'icon' => 'fa-user',
                'title' => $fullName !== '' ? $fullName : ('Owner #' . $owner->id),
                'subtitle' => (string) ($owner->user?->email ?? 'No email'),
                'category' => 'Pet Owners',
                'url' => route('admin.pet-owners.show', $owner->id),
            ]);
        }

        $appointments = Appointment::with(['pet', 'pet.owner.user'])
            ->where(function ($query) use ($like) {
                $query->where('type', 'like', $like)
                    ->orWhere('status', 'like', $like)
                    ->orWhereHas('pet', function ($petQuery) use ($like) {
                        $petQuery->where('name', 'like', $like);
                    })
                    ->orWhereHas('pet.owner.user', function ($ownerUser) use ($like) {
                        $ownerUser->where('first_name', 'like', $like)
                            ->orWhere('last_name', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    });
            })
            ->orderByDesc('appointment_date')
            ->limit(5)
            ->get();

        foreach ($appointments as $appointment) {
            $petName = (string) ($appointment->pet?->name ?? 'Unknown pet');
            $dateText = $appointment->appointment_date
                ? Carbon::parse($appointment->appointment_date)->format('M d, Y g:i A')
                : 'TBD';

            $results->push([
                'type' => 'Appointment',
                'icon' => 'fa-calendar-check',
                'title' => ucfirst(str_replace('_', ' ', (string) $appointment->type)) . ' - ' . $petName,
                'subtitle' => $dateText,
                'category' => 'Appointments',
                'url' => route('admin.appointments.show', $appointment->id),
            ]);
        }

        $orders = Order::with(['owner.user'])
            ->where(function ($query) use ($like) {
                $query->where('id', 'like', $like)
                    ->orWhere('status', 'like', $like)
                    ->orWhere('order_type', 'like', $like)
                    ->orWhereHas('owner.user', function ($ownerUser) use ($like) {
                        $ownerUser->where('first_name', 'like', $like)
                            ->orWhere('last_name', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    });
            })
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        foreach ($orders as $order) {
            $ownerName = trim((string) (($order->owner?->user?->first_name ?? '') . ' ' . ($order->owner?->user?->last_name ?? '')));

            $results->push([
                'type' => 'Order',
                'icon' => 'fa-shopping-bag',
                'title' => 'Order #' . $order->id,
                'subtitle' => ($ownerName !== '' ? $ownerName : 'Unknown owner') . ' - ' . ucfirst(str_replace('_', ' ', (string) $order->status)),
                'category' => 'Orders',
                'url' => route('admin.orders.show', $order->id),
            ]);
        }

        return response()->json([
            'results' => $results->take(20)->values(),
        ]);
    }
}
