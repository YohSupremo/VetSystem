<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\IncidentNote;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
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

    public function index()
    {
        $incidents = Incident::with(['pet', 'reportedBy'])
            ->orderBy('incident_date', 'desc')
            ->get();

        return view('admin.incidents.index', compact('incidents'));
    }

    public function show($id)
    {
        $incident = Incident::with(['pet.owner.user', 'reportedBy', 'incidentNotes.addedBy'])
            ->findOrFail($id);

        return view('admin.incidents.show', [
            'incident' => $incident,
            'responders' => $this->responderOptions(),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function update(Request $request, $id)
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
}
