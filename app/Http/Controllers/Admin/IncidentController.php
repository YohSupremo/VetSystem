<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cage;
use App\Models\Incident;
use App\Models\IncidentNote;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    private function incidentTypes(): array
    {
        return [
            'pet_injury' => 'Pet Injury',
            'pet_illness' => 'Pet Illness',
            'pet_escape' => 'Pet Escape',
            'pet_aggression' => 'Pet Aggression',
            'staff_injury' => 'Staff Injury',
            'visitor_injury' => 'Visitor Injury',
            'medication_error' => 'Medication Error',
            'equipment_failure' => 'Equipment Failure',
            'facility_damage' => 'Facility Damage',
            'other' => 'Other',
        ];
    }

    private function severityOptions(): array
    {
        return [
            'minor' => 'Minor',
            'moderate' => 'Moderate',
            'severe' => 'Severe',
            'critical' => 'Critical',
        ];
    }

    private function responderOptions(): array
    {
        return [
            'On-call Veterinarian',
            'Emergency Vet Team',
            'Animal Control',
            'Security Team',
            'Operations Manager',
            'Facility Maintenance',
            'Local Emergency Services',
        ];
    }

    private function statusOptions(): array
    {
        return ['open', 'investigating', 'resolved', 'closed'];
    }

    private function generateIncidentNumber(): string
    {
        $prefix = 'INC-' . now()->format('Y');
        $sequence = Incident::where('incident_number', 'like', $prefix . '%')->count() + 1;

        do {
            $number = sprintf('%s-%06d', $prefix, $sequence);
            $sequence++;
        } while (Incident::where('incident_number', $number)->exists());

        return $number;
    }

    private function validationRules(bool $isUpdate = false): array
    {
        $rules = [
            'incident_date' => ['required', 'date'],
            'incident_type' => ['required', 'in:' . implode(',', array_keys($this->incidentTypes()))],
            'severity' => ['required', 'in:' . implode(',', array_keys($this->severityOptions()))],
            'pet_id' => ['nullable', 'exists:pets,id'],
            'affected_user_id' => ['nullable', 'exists:users,id'],
            'location' => ['required', 'string', 'max:150'],
            'cage_id' => ['nullable', 'exists:cages,id'],
            'description' => ['required', 'string'],
            'immediate_action_taken' => ['nullable', 'string'],
            'root_cause' => ['nullable', 'string'],
            'corrective_action' => ['nullable', 'string'],
            'status' => ['required', 'in:' . implode(',', $this->statusOptions())],
            'reported_by' => ['nullable', 'exists:users,id'],
        ];

        if (!$isUpdate) {
            $rules['incident_number'] = ['nullable', 'string', 'max:50'];
        }

        return $rules;
    }

    private function formData(): array
    {
        return [
            'pets' => Pet::with('owner.user')->orderBy('name')->get(),
            'users' => User::where('is_active', 1)->orderBy('first_name')->orderBy('last_name')->get(),
            'cages' => Cage::orderBy('cage_code')->get(),
            'incidentTypes' => $this->incidentTypes(),
            'severityOptions' => $this->severityOptions(),
            'statusOptions' => $this->statusOptions(),
        ];
    }

    public function index()
    {
        $incidents = Incident::with(['pet', 'reportedBy'])
            ->orderBy('incident_date', 'desc')
            ->paginate(15);

        return view('admin.incidents.index', compact('incidents'));
    }

    public function create()
    {
        return view('admin.incidents.create', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());

        $status = $validated['status'];
        $resolvedDate = in_array($status, ['resolved', 'closed'], true) ? now() : null;

        $incident = Incident::create([
            'incident_number' => $validated['incident_number'] ?: $this->generateIncidentNumber(),
            'incident_date' => $validated['incident_date'],
            'incident_type' => $validated['incident_type'],
            'severity' => $validated['severity'],
            'pet_id' => $validated['pet_id'] ?? null,
            'affected_user_id' => $validated['affected_user_id'] ?? null,
            'location' => $validated['location'],
            'cage_id' => $validated['cage_id'] ?? null,
            'description' => $validated['description'],
            'immediate_action_taken' => $validated['immediate_action_taken'] ?? null,
            'root_cause' => $validated['root_cause'] ?? null,
            'corrective_action' => $validated['corrective_action'] ?? null,
            'status' => $status,
            'resolved_date' => $resolvedDate,
            'reported_by' => $validated['reported_by'] ?? auth()->id(),
            'reported_at' => now(),
        ]);

        return redirect()->route('admin.incidents.show', $incident->id)
            ->with('success', 'Incident created successfully.');
    }

    public function show($id)
    {
        $incident = Incident::with(['pet.owner.user', 'reportedBy', 'affectedUser', 'cage', 'incidentNotes.addedBy'])
            ->findOrFail($id);

        return view('admin.incidents.show', [
            'incident' => $incident,
            'responders' => $this->responderOptions(),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function edit($id)
    {
        $incident = Incident::findOrFail($id);

        return view('admin.incidents.edit', array_merge($this->formData(), [
            'incident' => $incident,
        ]));
    }

    public function update(Request $request, $id)
    {
        $incident = Incident::findOrFail($id);

        $validated = $request->validate($this->validationRules(true));

        $status = $validated['status'];
        $resolvedDate = in_array($status, ['resolved', 'closed'], true)
            ? ($incident->resolved_date ?? now())
            : null;

        $incident->update([
            'incident_date' => $validated['incident_date'],
            'incident_type' => $validated['incident_type'],
            'severity' => $validated['severity'],
            'pet_id' => $validated['pet_id'] ?? null,
            'affected_user_id' => $validated['affected_user_id'] ?? null,
            'location' => $validated['location'],
            'cage_id' => $validated['cage_id'] ?? null,
            'description' => $validated['description'],
            'immediate_action_taken' => $validated['immediate_action_taken'] ?? null,
            'root_cause' => $validated['root_cause'] ?? null,
            'corrective_action' => $validated['corrective_action'] ?? null,
            'status' => $status,
            'resolved_date' => $resolvedDate,
            'reported_by' => $validated['reported_by'] ?? $incident->reported_by,
        ]);

        return redirect()->route('admin.incidents.show', $incident->id)
            ->with('success', 'Incident updated successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $incident = Incident::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:open,investigating,resolved,closed',
            'responder' => 'nullable|string|max:150',
            'note' => 'nullable|string',
        ]);

        $incident->status = $validated['status'];
        if (in_array($incident->status, ['resolved', 'closed'], true) && !$incident->resolved_date) {
            $incident->resolved_date = now();
        } elseif (!in_array($incident->status, ['resolved', 'closed'], true)) {
            $incident->resolved_date = null;
        }
        $incident->save();

        $noteParts = [];
        if (!empty($validated['responder'])) {
            $noteParts[] = 'Responder selected: ' . $validated['responder'];
        }
        if (!empty($validated['note'])) {
            $noteParts[] = trim($validated['note']);
        }

        if (!empty($noteParts)) {
            $adminId = auth()->id() ?? 1;
            IncidentNote::create([
                'incident_id' => $incident->id,
                'note' => implode("\n", $noteParts),
                'added_by' => $adminId,
                'added_at' => now(),
            ]);
        }

        return redirect()->route('admin.incidents.show', $incident->id)
            ->with('success', 'Incident updated successfully.');
    }

    public function destroy($id)
    {
        $incident = Incident::findOrFail($id);
        $incident->delete();

        return redirect()->route('admin.incidents.index')
            ->with('success', 'Incident deleted successfully.');
    }
}
