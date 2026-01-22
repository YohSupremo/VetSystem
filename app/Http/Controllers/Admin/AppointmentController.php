<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /**
     * Available appointment types and statuses.
     */
    protected array $types = [
        'consultation',
        'vaccination',
        'surgery',
        'grooming',
        'boarding',
        'follow_up',
        'other',
    ];

    protected array $statuses = [
        'pending',
        'confirmed',
        'in_progress',
        'completed',
        'cancelled',
        'no_show',
    ];

    public function index(Request $request): View
    {
        $hasAppointments = Schema::hasTable('appointments');
        $appointments = collect();
        $filters = [
            'status' => $request->query('status'),
            'type' => $request->query('type'),
        ];

        if ($hasAppointments) {
            $appointments = DB::table('appointments')
                ->leftJoin('pets', 'appointments.pet_id', '=', 'pets.id')
                ->leftJoin('pet_owners', 'pets.owner_id', '=', 'pet_owners.id')
                ->leftJoin('users as owners', 'pet_owners.user_id', '=', 'owners.id')
                ->leftJoin('users as vets', 'appointments.veterinarian_id', '=', 'vets.id')
                ->when($filters['status'], function ($query, $status) {
                    return $query->where('appointments.status', $status);
                })
                ->when($filters['type'], function ($query, $type) {
                    return $query->where('appointments.type', $type);
                })
                ->orderByDesc('appointments.appointment_date')
                ->limit(100)
                ->get([
                    'appointments.id',
                    'appointments.pet_id',
                    'appointments.veterinarian_id',
                    'appointments.appointment_date',
                    'appointments.type',
                    'appointments.status',
                    'appointments.notes',
                    'pets.name as pet_name',
                    DB::raw("COALESCE(pets.species, '') as pet_species"),
                    DB::raw("TRIM(CONCAT(owners.first_name, ' ', owners.last_name)) as owner_name"),
                    DB::raw("TRIM(CONCAT(vets.first_name, ' ', vets.last_name)) as veterinarian_name"),
                ])
                ->map(function ($appointment) {
                    $appointment->formatted_date = $appointment->appointment_date
                        ? Carbon::parse($appointment->appointment_date)->format('M d, Y g:i A')
                        : 'TBD';
                    $appointment->type_label = $appointment->type
                        ? ucfirst(str_replace('_', ' ', $appointment->type))
                        : 'Unknown';
                    $appointment->status_label = $appointment->status
                        ? ucfirst(str_replace('_', ' ', $appointment->status))
                        : 'Unknown';
                    $appointment->status_badge = match ($appointment->status) {
                        'completed' => 'success',
                        'cancelled', 'no_show' => 'danger',
                        default => 'warning',
                    };

                    return $appointment;
                });
        }

        return view('admin.appointments.index', [
            'appointments' => $appointments,
            'hasAppointments' => $hasAppointments,
            'types' => $this->types,
            'statuses' => $this->statuses,
            'filters' => $filters,
        ]);
    }

    public function create(): View|RedirectResponse
    {
        if (!Schema::hasTable('appointments')) {
            return redirect()->route('admin.appointments.index')
                ->with('error', 'Appointments table is not available. Run migrations to enable scheduling.');
        }

        [$pets, $veterinarians] = $this->formSelections();

        return view('admin.appointments.create', [
            'pets' => $pets,
            'veterinarians' => $veterinarians,
            'types' => $this->types,
            'statuses' => $this->statuses,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (!Schema::hasTable('appointments')) {
            return redirect()->route('admin.appointments.index')
                ->with('error', 'Appointments table is not available. Run migrations to enable scheduling.');
        }

        $validated = $request->validate([
            'pet_id' => ['required', 'integer'],
            'veterinarian_id' => ['nullable', 'integer'],
            'appointment_date' => ['required', 'date'],
            'type' => ['required', 'in:' . implode(',', $this->types)],
            'status' => ['required', 'in:' . implode(',', $this->statuses)],
            'notes' => ['nullable', 'string'],
        ]);

        DB::table('appointments')->insert([
            'pet_id' => $validated['pet_id'],
            'veterinarian_id' => $validated['veterinarian_id'],
            'appointment_date' => Carbon::parse($validated['appointment_date'])->format('Y-m-d H:i:s'),
            'type' => $validated['type'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.appointments.index')
            ->with('success', 'Appointment created successfully.');
    }

    public function show(int $appointment): View
    {
        $this->ensureAppointmentsTable();

        $record = $this->loadAppointment($appointment);

        return view('admin.appointments.show', [
            'appointment' => $record,
        ]);
    }

    public function edit(int $appointment): View
    {
        $this->ensureAppointmentsTable();

        $record = DB::table('appointments')->where('id', $appointment)->first();

        abort_if(!$record, 404);

        $record->appointment_date_input = $record->appointment_date
            ? Carbon::parse($record->appointment_date)->format('Y-m-d\TH:i')
            : null;

        [$pets, $veterinarians] = $this->formSelections();

        return view('admin.appointments.edit', [
            'appointment' => $record,
            'pets' => $pets,
            'veterinarians' => $veterinarians,
            'types' => $this->types,
            'statuses' => $this->statuses,
        ]);
    }

    public function update(Request $request, int $appointment): RedirectResponse
    {
        $this->ensureAppointmentsTable();

        $record = DB::table('appointments')->where('id', $appointment)->first();

        abort_if(!$record, 404);

        $validated = $request->validate([
            'pet_id' => ['required', 'integer'],
            'veterinarian_id' => ['nullable', 'integer'],
            'appointment_date' => ['required', 'date'],
            'type' => ['required', 'in:' . implode(',', $this->types)],
            'status' => ['required', 'in:' . implode(',', $this->statuses)],
            'notes' => ['nullable', 'string'],
        ]);

        DB::table('appointments')->where('id', $appointment)->update([
            'pet_id' => $validated['pet_id'],
            'veterinarian_id' => $validated['veterinarian_id'],
            'appointment_date' => Carbon::parse($validated['appointment_date'])->format('Y-m-d H:i:s'),
            'type' => $validated['type'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully.');
    }

    public function destroy(int $appointment): RedirectResponse
    {
        $this->ensureAppointmentsTable();

        DB::table('appointments')->where('id', $appointment)->delete();

        return redirect()->route('admin.appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    /**
     * Ensure appointments table exists.
     */
    protected function ensureAppointmentsTable(): void
    {
        abort_if(!Schema::hasTable('appointments'), 404, 'Appointments table not found.');
    }

    /**
     * Load appointment with related names.
     */
    protected function loadAppointment(int $appointment): object
    {
        $record = DB::table('appointments')
            ->leftJoin('pets', 'appointments.pet_id', '=', 'pets.id')
            ->leftJoin('pet_owners', 'pets.owner_id', '=', 'pet_owners.id')
            ->leftJoin('users as owners', 'pet_owners.user_id', '=', 'owners.id')
            ->leftJoin('users as vets', 'appointments.veterinarian_id', '=', 'vets.id')
            ->where('appointments.id', $appointment)
            ->first([
                'appointments.*',
                'pets.name as pet_name',
                DB::raw("COALESCE(pets.species, '') as pet_species"),
                DB::raw("TRIM(CONCAT(owners.first_name, ' ', owners.last_name)) as owner_name"),
                DB::raw("TRIM(CONCAT(vets.first_name, ' ', vets.last_name)) as veterinarian_name"),
            ]);

        abort_if(!$record, 404);

        $record->formatted_date = $record->appointment_date
            ? Carbon::parse($record->appointment_date)->format('M d, Y g:i A')
            : 'TBD';
        $record->type_label = $record->type
            ? ucfirst(str_replace('_', ' ', $record->type))
            : 'Unknown';
        $record->status_label = $record->status
            ? ucfirst(str_replace('_', ' ', $record->status))
            : 'Unknown';

        return $record;
    }

    /**
     * Gather select options for forms.
     */
    protected function formSelections(): array
    {
        $pets = Schema::hasTable('pets')
            ? DB::table('pets')->orderBy('name')->get(['id', 'name', 'species'])
            : new Collection();

        $veterinarians = Schema::hasTable('users')
            ? DB::table('users')
                ->where('role', 'veterinarian')
                ->orderBy('first_name')
                ->get(['id', 'first_name', 'last_name'])
            : new Collection();

        return [$pets, $veterinarians];
    }
}
