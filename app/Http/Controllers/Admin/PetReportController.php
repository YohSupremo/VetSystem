<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Pet;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\PetVaccination;
use App\Models\Appointment;
use App\Models\Surgery;
use App\Models\Incident;
use App\Models\ChronicCondition;
use App\Models\PetAllergy;
use App\Models\CageAssignment;
use App\Models\GroomingAppointment;
use App\Models\LabRequisition;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PetReportController extends BaseController
{
    /**
     * Display the Pet Reports index page.
     */
    public function index(Request $request)
    {
        $pets = Pet::with(['owner.user'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.pet-reports.index', compact('pets'));
    }

    /**
     * Get pet data for AJAX requests.
     */
    public function getPetData($petId)
    {
        $pet = Pet::with(['owner.user'])->find($petId);
        
        if (!$pet) {
            return response()->json(['error' => 'Pet not found'], 404);
        }

        // Get statistics
        $stats = $this->getPetStatistics($petId);
        
        // Get chart data
        $charts = $this->getChartData($petId);
        
        // Get table data
        $tables = $this->getTableData($petId);

        return response()->json([
            'pet' => [
                'id' => $pet->id,
                'name' => $pet->name,
                'species' => $pet->species,
                'breed' => $pet->breed,
                'age' => $pet->age,
                'owner_name' => $pet->owner->user->first_name . ' ' . $pet->owner->user->last_name,
                'owner_email' => $pet->owner->user->email,
            ],
            'stats' => $stats,
            'charts' => $charts,
            'tables' => $tables
        ]);
    }

    /**
     * Get pet statistics.
     */
    private function getPetStatistics($petId)
    {
        return [
            'total_visits' => Appointment::where('pet_id', $petId)->count(),
            'total_prescriptions' => Prescription::whereHas('medicalRecord', function($query) use ($petId) {
                $query->where('pet_id', $petId);
            })->count(),
            'total_vaccinations' => PetVaccination::where('pet_id', $petId)->count(),
            'total_medical_records' => MedicalRecord::where('pet_id', $petId)->count(),
            'total_surgeries' => Surgery::where('pet_id', $petId)->count(),
            'total_incidents' => Incident::where('pet_id', $petId)->count(),
            'total_chronic_conditions' => ChronicCondition::where('pet_id', $petId)->count(),
            'total_allergies' => PetAllergy::where('pet_id', $petId)->count(),
            'total_cage_assignments' => CageAssignment::where('pet_id', $petId)->count(),
            'total_grooming_appointments' => GroomingAppointment::whereHas('appointment', function($query) use ($petId) {
                $query->where('pet_id', $petId);
            })->count(),
            'total_lab_tests' => LabRequisition::whereHas('medicalRecord', function($query) use ($petId) {
                $query->where('pet_id', $petId);
            })->count(),
        ];
    }

    /**
     * Get chart data for the pet.
     */
    private function getChartData($petId)
    {
        // Monthly visits data (last 12 months)
        $monthlyVisits = Appointment::where('pet_id', $petId)
            ->where('appointment_date', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(appointment_date, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fill missing months with 0
        $visitsData = [];
        $visitsLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $visitsLabels[] = now()->subMonths($i)->format('M Y');
            $visitsData[] = $monthlyVisits->where('month', $month)->first()?->count ?? 0;
        }

        // Treatment distribution data
        $treatments = MedicalRecord::where('pet_id', $petId)
            ->whereNotNull('treatment_plan')
            ->select('treatment_plan', DB::raw('COUNT(*) as count'))
            ->groupBy('treatment_plan')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Surgery types distribution
        $surgeries = Surgery::where('pet_id', $petId)
            ->with('surgeryType')
            ->select('surgery_type_id', DB::raw('COUNT(*) as count'))
            ->groupBy('surgery_type_id')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(function($surgery) {
                return [
                    'type' => $surgery->surgeryType ? $surgery->surgeryType->name : 'Unknown',
                    'count' => $surgery->count
                ];
            });

        // Incident types distribution
        $incidents = Incident::where('pet_id', $petId)
            ->select('incident_type', DB::raw('COUNT(*) as count'))
            ->groupBy('incident_type')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return [
            'visits' => [
                'labels' => $visitsLabels,
                'data' => $visitsData
            ],
            'treatments' => [
                'labels' => $treatments->pluck('treatment_plan')->map(function($treatment) {
                    return \Illuminate\Support\Str::limit($treatment, 30);
                })->toArray(),
                'data' => $treatments->pluck('count')->toArray()
            ],
            'surgeries' => [
                'labels' => $surgeries->pluck('type')->toArray(),
                'data' => $surgeries->pluck('count')->toArray()
            ],
            'incidents' => [
                'labels' => $incidents->pluck('incident_type')->toArray(),
                'data' => $incidents->pluck('count')->toArray()
            ]
        ];
    }

    /**
     * Get table data for the pet.
     */
    private function getTableData($petId)
    {
        // Medical Records
        $medicalRecords = MedicalRecord::where('pet_id', $petId)
            ->with('veterinarian')
            ->orderBy('visit_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function($record) {
                return [
                    'visit_date' => $record->visit_date,
                    'diagnosis' => $record->diagnosis,
                    'vet_name' => $record->veterinarian ? $record->veterinarian->first_name . ' ' . $record->veterinarian->last_name : 'N/A',
                ];
            });

        // Prescriptions
        $prescriptions = Prescription::whereHas('medicalRecord', function($query) use ($petId) {
            $query->where('pet_id', $petId);
        })
        ->with('medication')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get()
        ->map(function($prescription) {
            return [
                'created_at' => $prescription->created_at,
                'medication_name' => $prescription->medication->name ?? 'N/A',
                'dosage' => $prescription->dosage ?? 'N/A',
                'status' => $prescription->status ?? 'active',
            ];
        });

        // Appointments
        $appointments = Appointment::where('pet_id', $petId)
            ->with('veterinarian')
            ->orderBy('appointment_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function($appointment) {
                return [
                    'appointment_date' => $appointment->appointment_date,
                    'type' => $appointment->type ?? 'General',
                    'status' => $appointment->status ?? 'scheduled',
                    'vet_name' => $appointment->veterinarian ? $appointment->veterinarian->first_name . ' ' . $appointment->veterinarian->last_name : 'N/A',
                ];
            });

        // Vaccinations
        $vaccinations = PetVaccination::where('pet_id', $petId)
            ->orderBy('administered_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function($vaccination) {
                return [
                    'administered_date' => $vaccination->administered_date,
                    'vaccine_name' => $vaccination->vaccine_name ?? 'N/A',
                    'next_due_date' => $vaccination->next_due_date,
                ];
            });

        // Surgeries
        $surgeries = Surgery::where('pet_id', $petId)
            ->with(['surgeon', 'surgeryType'])
            ->orderBy('scheduled_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function($surgery) {
                return [
                    'scheduled_date' => $surgery->scheduled_date,
                    'surgery_type' => $surgery->surgeryType ? $surgery->surgeryType->name : 'N/A',
                    'surgeon_name' => $surgery->surgeon ? $surgery->surgeon->first_name . ' ' . $surgery->surgeon->last_name : 'N/A',
                    'outcome' => $surgery->outcome ?? 'N/A',
                    'status' => $surgery->status ?? 'scheduled',
                ];
            });

        // Incidents
        $incidents = Incident::where('pet_id', $petId)
            ->with('reportedBy')
            ->orderBy('incident_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function($incident) {
                return [
                    'incident_date' => $incident->incident_date,
                    'incident_type' => $incident->incident_type,
                    'severity' => $incident->severity,
                    'status' => $incident->status,
                    'reported_by' => $incident->reportedBy ? $incident->reportedBy->first_name . ' ' . $incident->reportedBy->last_name : 'N/A',
                ];
            });

        // Chronic Conditions
        $chronicConditions = ChronicCondition::where('pet_id', $petId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($condition) {
                return [
                    'created_at' => $condition->created_at,
                    'condition_name' => $condition->condition_name,
                    'severity' => $condition->severity ?? 'N/A',
                    'status' => $condition->status ?? 'active',
                    'management_plan' => $condition->management_plan ? \Illuminate\Support\Str::limit($condition->management_plan, 50) : 'N/A',
                ];
            });

        // Allergies
        $allergies = PetAllergy::where('pet_id', $petId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($allergy) {
                return [
                    'created_at' => $allergy->created_at,
                    'allergen' => $allergy->allergen,
                    'severity' => $allergy->severity ?? 'N/A',
                    'reaction_symptoms' => $allergy->reaction_symptoms ? \Illuminate\Support\Str::limit($allergy->reaction_symptoms, 50) : 'N/A',
                    'status' => $allergy->status ?? 'active',
                ];
            });

        // Cage Assignments
        $cageAssignments = CageAssignment::where('pet_id', $petId)
            ->with('cage')
            ->orderBy('start_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function($assignment) {
                return [
                    'start_date' => $assignment->start_date,
                    'end_date' => $assignment->end_date,
                    'cage_name' => $assignment->cage ? $assignment->cage->name : 'N/A',
                    'reason' => $assignment->reason ?? 'N/A',
                    'status' => $assignment->status ?? 'active',
                ];
            });

        // Grooming Appointments
        $groomingAppointments = GroomingAppointment::whereHas('appointment', function($query) use ($petId) {
                $query->where('pet_id', $petId);
            })
            ->with(['appointment', 'service', 'groomer'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($grooming) {
                return [
                    'appointment_date' => $grooming->appointment ? $grooming->appointment->appointment_date : 'N/A',
                    'service_name' => $grooming->service ? $grooming->service->name : 'N/A',
                    'groomer_name' => $grooming->groomer ? $grooming->groomer->first_name . ' ' . $grooming->groomer->last_name : 'N/A',
                    'status' => $grooming->status ?? 'scheduled',
                    'special_instructions' => $grooming->special_instructions ? \Illuminate\Support\Str::limit($grooming->special_instructions, 50) : 'N/A',
                ];
            });

        // Laboratory Tests
        $labTests = LabRequisition::whereHas('medicalRecord', function($query) use ($petId) {
                $query->where('pet_id', $petId);
            })
            ->with(['test', 'medicalRecord'])
            ->orderBy('requested_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function($requisition) {
                return [
                    'requested_date' => $requisition->requested_date,
                    'test_name' => $requisition->test ? $requisition->test->name : 'N/A',
                    'status' => $requisition->status ?? 'pending',
                    'result_date' => $requisition->result_date,
                    'results' => $requisition->results ? \Illuminate\Support\Str::limit($requisition->results, 50) : 'N/A',
                ];
            });

        return [
            'medical_records' => $medicalRecords->toArray(),
            'prescriptions' => $prescriptions->toArray(),
            'appointments' => $appointments->toArray(),
            'vaccinations' => $vaccinations->toArray(),
            'surgeries' => $surgeries->toArray(),
            'incidents' => $incidents->toArray(),
            'chronic_conditions' => $chronicConditions->toArray(),
            'allergies' => $allergies->toArray(),
            'cage_assignments' => $cageAssignments->toArray(),
            'grooming_appointments' => $groomingAppointments->toArray(),
            'lab_tests' => $labTests->toArray(),
        ];
    }

    /**
     * Export pet report to PDF.
     */
    public function exportPetReport($petId)
    {
        $pet = Pet::with(['owner.user'])->find($petId);
        
        if (!$pet) {
            abort(404, 'Pet not found');
        }

        // Get all data for the report
        $stats = $this->getPetStatistics($petId);
        $charts = $this->getChartData($petId);
        $tables = $this->getTableData($petId);

        // Generate HTML for PDF
        $html = view('admin.pet-reports.pdf', compact(
            'pet', 'stats', 'charts', 'tables'
        ))->render();

        // Create PDF
        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "pet_report_{$pet->name}_" . date('Y-m-d') . ".pdf";
        
        return response($dompdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Content-Length', strlen($dompdf->output()));
    }
}
