<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabRequisition;
use App\Models\LabTest;
use App\Models\MedicalRecord;
use App\Models\ClinicSetting;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Pet;
use App\Models\TestRequest;
use App\Models\TestType;
use App\Models\TestResult;
use App\Models\User;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaboratoryController extends Controller
{
    /**
     * Schema-based Laboratory Dashboard (lab_requisitions + lab_tests)
     */
    public function dashboard()
    {
        $pendingRequisitions = LabRequisition::where('status', 'pending')->count();
        $completedRequisitions = LabRequisition::where('status', 'completed')->count();
        $totalLabTests = LabTest::count();

        $requisitions = LabRequisition::with([
            'medicalRecord.pet.owner.user',
            'test',
            'requestedBy',
            'invoice.invoiceItems',
            'invoice.payments',
        ])
            ->orderByDesc('requested_date')
            ->paginate(10);

        $requisitions->getCollection()->transform(function ($req) {
            if ($req->status === 'completed' && !$req->invoice_id) {
                try {
                    $invoice = $this->ensureLabRequisitionInvoice($req);
                    $req->setRelation('invoice', $invoice);
                    $req->invoice_id = $invoice->id;
                } catch (\Throwable $e) {
                    // Keep requisition visible even if invoice backfill fails for incomplete legacy records.
                }
            }

            return $req;
        });

        return view('admin.laboratory.index', compact(
            'pendingRequisitions',
            'completedRequisitions',
            'totalLabTests',
            'requisitions'
        ));
    }

    /**
     * Lab Tests (Catalog)
     */
    public function testsIndex()
    {
        $labTests = LabTest::orderBy('test_name')->paginate(15);
        return view('admin.laboratory.tests.index', compact('labTests'));
    }

    public function testsCreate()
    {
        $categories = ['blood', 'urine', 'fecal', 'biopsy', 'cytology', 'other'];
        return view('admin.laboratory.tests.create', compact('categories'));
    }

    public function testsStore(Request $request)
    {
        $data = $request->validate([
            'test_name' => 'required|string|max:150',
            'category' => 'required|in:blood,urine,fecal,biopsy,cytology,other',
            'description' => 'nullable|string',
            'standard_price' => 'nullable|numeric|min:0',
        ]);

        $labTest = LabTest::create($data);

        return redirect()->route('admin.laboratory.tests.show', $labTest->id)
            ->with('success', 'Lab test created successfully.');
    }

    public function testsShow(LabTest $labTest)
    {
        $labTest->loadCount('labRequisitions');
        return view('admin.laboratory.tests.show', compact('labTest'));
    }

    public function testsEdit(LabTest $labTest)
    {
        $categories = ['blood', 'urine', 'fecal', 'biopsy', 'cytology', 'other'];
        return view('admin.laboratory.tests.edit', compact('labTest', 'categories'));
    }

    public function testsUpdate(Request $request, LabTest $labTest)
    {
        $data = $request->validate([
            'test_name' => 'required|string|max:150',
            'category' => 'required|in:blood,urine,fecal,biopsy,cytology,other',
            'description' => 'nullable|string',
            'standard_price' => 'nullable|numeric|min:0',
        ]);

        $labTest->update($data);

        return redirect()->route('admin.laboratory.tests.show', $labTest->id)
            ->with('success', 'Lab test updated successfully.');
    }

    public function testsDestroy(LabTest $labTest)
    {
        if ($labTest->labRequisitions()->exists()) {
            return redirect()->route('admin.laboratory.tests.index')
                ->with('error', 'Cannot delete a test that has requisitions.');
        }

        $labTest->delete();

        return redirect()->route('admin.laboratory.tests.index')
            ->with('success', 'Lab test deleted successfully.');
    }

    /**
     * Lab Requisitions (Requests / Results)
     */
    public function requisitionsCreate()
    {
        $medicalRecords = MedicalRecord::with(['pet.owner.user', 'veterinarian'])
            ->orderByDesc('visit_date')
            ->limit(50)
            ->get();

        $labTests = LabTest::orderBy('test_name')->get();

        $requesters = User::orderBy('first_name')->orderBy('last_name')->get();

        return view('admin.laboratory.requisitions.create', compact('medicalRecords', 'labTests', 'requesters'));
    }

    public function requisitionsStore(Request $request, NotificationService $notificationService)
    {
        $data = $request->validate([
            'medical_record_id' => 'required|exists:medical_records,id',
            'test_id' => 'required|exists:lab_tests,id',
            'requested_by' => 'required|exists:users,id',
            'requested_date' => 'nullable|date',
            'sample_collected' => 'nullable|boolean',
            'sample_collection_date' => 'nullable|date',
            'status' => 'required|in:pending,collected,sent_to_lab,completed,cancelled',
            'results' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if (!isset($data['requested_date']) || !$data['requested_date']) {
            $data['requested_date'] = now();
        }

        $data['sample_collected'] = (bool)($data['sample_collected'] ?? false);

        if ($data['sample_collected'] && empty($data['sample_collection_date'])) {
            $data['sample_collection_date'] = now();
        }

        $labRequisition = LabRequisition::create($data);

        if ($labRequisition->status === 'completed') {
            $this->ensureLabRequisitionInvoice($labRequisition);
            $this->notifyLabResult($notificationService, $labRequisition);
        } elseif ($labRequisition->status === 'cancelled') {
            $this->cancelLabRequisitionInvoice($labRequisition);
        }

        return redirect()->route('admin.laboratory.requisitions.show', $labRequisition->id)
            ->with('success', 'Lab requisition created successfully.');
    }

    public function requisitionsShow(LabRequisition $labRequisition)
    {
        $labRequisition->load(['medicalRecord.pet.owner.user', 'medicalRecord.veterinarian', 'test', 'requestedBy']);
        return view('admin.laboratory.requisitions.show', compact('labRequisition'));
    }

    public function requisitionsEdit(LabRequisition $labRequisition)
    {
        $labRequisition->load(['medicalRecord.pet.owner.user', 'test', 'requestedBy']);

        $labTests = LabTest::orderBy('test_name')->get();
        $requesters = User::orderBy('first_name')->orderBy('last_name')->get();

        return view('admin.laboratory.requisitions.edit', compact('labRequisition', 'labTests', 'requesters'));
    }

    public function requisitionsUpdate(Request $request, LabRequisition $labRequisition, NotificationService $notificationService)
    {
        $previousStatus = $labRequisition->status;
        $data = $request->validate([
            'test_id' => 'required|exists:lab_tests,id',
            'requested_by' => 'required|exists:users,id',
            'requested_date' => 'required|date',
            'sample_collected' => 'nullable|boolean',
            'sample_collection_date' => 'nullable|date',
            'status' => 'required|in:pending,collected,sent_to_lab,completed,cancelled',
            'results' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $data['sample_collected'] = (bool)($data['sample_collected'] ?? false);

        if ($data['sample_collected'] && empty($data['sample_collection_date'])) {
            $data['sample_collection_date'] = now();
        }

        if (!$data['sample_collected']) {
            $data['sample_collection_date'] = null;
        }

        // Auto-set result_date if results are entered and date is missing
        if (!empty($data['results']) && is_null($labRequisition->result_date)) {
            $data['result_date'] = now();
            // Optional: reset notification if results are updated
            $data['result_notification_sent'] = false; 
        }

        $labRequisition->update($data);

        if ($labRequisition->status === 'completed') {
            $this->ensureLabRequisitionInvoice($labRequisition);
            if ($previousStatus !== 'completed') {
                $this->notifyLabResult($notificationService, $labRequisition);
            }
        } elseif ($labRequisition->status === 'cancelled') {
            $this->cancelLabRequisitionInvoice($labRequisition);
        }

        return redirect()->route('admin.laboratory.requisitions.show', $labRequisition->id)
            ->with('success', 'Lab requisition updated successfully.');
    }

    public function markRequisitionPaid($id)
    {
        $labRequisition = LabRequisition::with([
            'invoice.invoiceItems',
            'invoice.payments',
            'medicalRecord.pet',
            'test',
        ])->findOrFail($id);

        if ($labRequisition->status === 'cancelled') {
            return back()->withErrors(['error' => 'Cancelled requisitions cannot be marked as paid.']);
        }

        DB::beginTransaction();

        try {
            $invoice = $labRequisition->invoice ?: $this->ensureLabRequisitionInvoice($labRequisition);
            $invoice->load(['invoiceItems', 'payments']);

            if ($invoice->is_paid) {
                DB::commit();
                return back()->with('success', 'Laboratory invoice is already paid.');
            }

            $balance = (float) $invoice->balance;
            if ($balance <= 0) {
                $invoice->update(['status' => 'paid']);
                DB::commit();
                return back()->with('success', 'Laboratory invoice marked as paid.');
            }

            Payment::create([
                'invoice_id' => $invoice->id,
                'payment_date' => now(),
                'amount' => $balance,
                'payment_method' => 'cash',
                'reference_number' => null,
                'received_by' => Auth::id(),
                'notes' => 'Paid from laboratory requisitions list.',
            ]);

            $invoice->load(['invoiceItems', 'payments']);
            $invoice->update(['status' => $invoice->balance <= 0 ? 'paid' : 'partial']);

            DB::commit();

            return back()->with('success', 'Laboratory payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Unable to mark requisition as paid: ' . $e->getMessage()]);
        }
    }

    private function ensureLabRequisitionInvoice(LabRequisition $labRequisition): Invoice
    {
        if ($labRequisition->invoice_id) {
            $existingInvoice = Invoice::with(['invoiceItems', 'payments'])->find($labRequisition->invoice_id);
            if ($existingInvoice) {
                return $existingInvoice;
            }
        }

        $labRequisition->loadMissing(['medicalRecord.pet', 'test']);

        $pet = $labRequisition->medicalRecord?->pet;
        $ownerId = $pet?->owner_id;
        if (!$pet || !$ownerId) {
            throw new \RuntimeException('Cannot create laboratory invoice: linked pet owner is missing.');
        }

        $prefix = ClinicSetting::invoicePrefix();
        $defaultTaxRate = ClinicSetting::defaultTaxRate();
        $year = now()->format('Y');
        $lastSequence = Invoice::where('invoice_prefix', $prefix)
            ->whereYear('issue_date', $year)
            ->max('invoice_sequence');
        $nextSequence = $lastSequence ? ((int) $lastSequence + 1) : 1;

        $invoice = Invoice::create([
            'invoice_number' => sprintf('%s-%s-%06d', $prefix, $year, $nextSequence),
            'order_id' => null,
            'appointment_id' => null,
            'pet_id' => $pet->id,
            'owner_id' => $ownerId,
            'invoice_prefix' => $prefix,
            'invoice_sequence' => $nextSequence,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'status' => 'pending',
            'tax_rate' => $defaultTaxRate,
            'discount_amount' => 0,
            'notes' => 'Laboratory requisition #' . $labRequisition->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_type' => 'lab_test',
            'description' => 'Laboratory: ' . ($labRequisition->test->test_name ?? 'Lab Test'),
            'quantity' => 1,
            'unit_price' => (float) ($labRequisition->test->standard_price ?? 0),
        ]);

        $labRequisition->invoice_id = $invoice->id;
        $labRequisition->save();

        return $invoice->load(['invoiceItems', 'payments']);
    }

    private function cancelLabRequisitionInvoice(LabRequisition $labRequisition): void
    {
        $invoice = Invoice::with('payments')
            ->where('id', $labRequisition->invoice_id)
            ->where('status', '!=', 'cancelled')
            ->first();

        if (!$invoice) {
            return;
        }

        $invoice->payments()->delete();
        $invoice->update(['status' => 'cancelled']);
    }

    private function notifyLabResult(NotificationService $notificationService, LabRequisition $labRequisition): void
    {
        $labRequisition->loadMissing(['medicalRecord.pet', 'test']);
        $petName = $labRequisition->medicalRecord?->pet?->name ?? 'Pet';
        $testName = $labRequisition->test?->test_name ?? 'Lab Test';

        $notificationService->sendToRole(
            'veterinarian',
            Notification::TYPE_LAB_TEST,
            'Lab Results Ready',
            'Results are available for ' . $petName . ' (' . $testName . ').',
            [
                'reference_type' => 'lab_test',
                'reference_id' => $labRequisition->test_id,
                'action_url' => route('admin.laboratory.requisitions.show', $labRequisition->id),
            ]
        );
    }

    public function requisitionsDestroy(LabRequisition $labRequisition)
    {
        $labRequisition->delete();

        return redirect()->route('admin.laboratory.index')
            ->with('success', 'Lab requisition deleted successfully.');
    }

    /**
     * Display the laboratory dashboard
     */
    public function index()
    {
        $pendingTests = TestRequest::where('status', 'pending')->count();
        $completedTests = TestRequest::where('status', 'completed')->count();
        $testTypes = TestType::count();
        $testRequests = TestRequest::with(['pet', 'pet.owner'])->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.laboratory.index', compact(
            'pendingTests',
            'completedTests',
            'testTypes',
            'testRequests'
        ));
    }

    /**
     * Show the form for creating a new test request
     */
    public function createTestRequest()
    {
        $pets = Pet::with('owner')->get();
        $testTypes = TestType::active()->get();
        
        return view('admin.laboratory.test-requests.create', compact('pets', 'testTypes'));
    }

    /**
     * Store a newly created test request
     */
    public function storeTestRequest(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'test_type_id' => 'required|exists:test_types,id',
            'requested_by' => 'required|string|max:255',
            'requested_date' => 'required|date',
            'priority' => 'required|in:low,medium,high',
            'clinical_notes' => 'nullable|string',
        ]);

        $testRequest = TestRequest::create([
            'pet_id' => $validated['pet_id'],
            'test_type_id' => $validated['test_type_id'],
            'requested_by' => $validated['requested_by'],
            'requested_date' => $validated['requested_date'],
            'priority' => $validated['priority'],
            'clinical_notes' => $validated['clinical_notes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.laboratory.test-requests.show', $testRequest)
            ->with('success', 'Test request created successfully.');
    }

    /**
     * Display the specified test request
     */
    public function showTestRequest(TestRequest $testRequest)
    {
        $testRequest->load(['pet.owner', 'testType', 'result']);
        return view('admin.laboratory.test-requests.show', compact('testRequest'));
    }

    /**
     * Update the status of a test request
     */
    public function updateTestStatus(Request $request, TestRequest $testRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'result' => 'required_if:status,completed',
            'result_notes' => 'nullable|string',
            'completed_by' => 'required_if:status,completed',
            'completed_at' => 'required_if:status,completed|date',
        ]);

        DB::transaction(function () use ($testRequest, $validated) {
            $testRequest->update(['status' => $validated['status']]);

            if ($validated['status'] === 'completed') {
                $testRequest->result()->updateOrCreate(
                    ['test_request_id' => $testRequest->id],
                    [
                        'result' => $validated['result'],
                        'notes' => $validated['result_notes'] ?? null,
                        'completed_by' => $validated['completed_by'],
                        'completed_at' => $validated['completed_at'],
                    ]
                );
            }
        });

        return response()->json(['message' => 'Test status updated successfully.']);
    }

    /**
     * Display a listing of test types
     */
    public function testTypes()
    {
        $testTypes = TestType::withCount('testRequests')->get();
        return view('admin.laboratory.test-types.index', compact('testTypes'));
    }

    /**
     * Store a newly created test type
     */
    public function storeTestType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:test_types,name',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reference_ranges' => 'required|array',
            'reference_ranges.*.name' => 'required|string|max:255',
            'reference_ranges.*.range' => 'required|string|max:255',
            'reference_ranges.*.unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'turnaround_time' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $testType = TestType::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'reference_ranges' => $validated['reference_ranges'],
            'price' => $validated['price'],
            'turnaround_time' => $validated['turnaround_time'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Test type created successfully.',
            'testType' => $testType
        ], 201);
    }

    /**
     * Remove the specified test type
     */
    public function destroyTestType(TestType $testType)
    {
        if ($testType->testRequests()->exists()) {
            return response()->json([
                'message' => 'Cannot delete test type with associated test requests.'
            ], 422);
        }

        $testType->delete();

        return response()->json([
            'message' => 'Test type deleted successfully.'
        ]);
    }

    /**
     * Display a listing of test results
     */
    public function testResults()
    {
        $testResults = TestResult::with(['testRequest.pet', 'testRequest.testType'])
            ->latest('completed_at')
            ->paginate(15);
            
        return view('admin.laboratory.test-results.index', compact('testResults'));
    }

    /**
     * Display the specified test result
     */
    public function showTestResult(TestResult $testResult)
    {
        $testResult->load(['testRequest.pet.owner', 'testRequest.testType']);
        return view('admin.laboratory.test-results.show', compact('testResult'));
    }

    /**
     * Store a newly created test result
     */
    public function storeTestResult(Request $request)
    {
        $validated = $request->validate([
            'test_request_id' => 'required|exists:test_requests,id',
            'result' => 'required|string|in:normal,abnormal,critical',
            'notes' => 'nullable|string',
            'completed_by' => 'required|string|max:255',
            'completed_at' => 'required|date',
        ]);

        $testResult = TestResult::create($validated);
        
        // Update the test request status
        $testRequest = TestRequest::find($validated['test_request_id']);
        $testRequest->update(['status' => 'completed']);

        return response()->json([
            'message' => 'Test result saved successfully.',
            'testResult' => $testResult
        ], 201);
    }

    /**
     * API: Get pet information
     */
    public function getPetInfo(Pet $pet)
    {
        $pet->load('owner', 'species');
        
        return response()->json([
            'id' => $pet->id,
            'name' => $pet->name,
            'species' => $pet->species->name,
            'breed' => $pet->breed,
            'age' => $pet->date_of_birth ? Carbon::parse($pet->date_of_birth)->diffForHumans(null, true) : 'Unknown',
            'owner' => [
                'id' => $pet->owner->id,
                'name' => $pet->owner->name,
                'email' => $pet->owner->email,
                'phone' => $pet->owner->phone,
            ]
        ]);
    }

    /**
     * API: Get test requests with filters
     */
    public function getTestRequests(Request $request)
    {
        $query = TestRequest::with(['pet', 'testType', 'result'])
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->priority, function($q) use ($request) {
                $q->where('priority', $request->priority);
            })
            ->when($request->search, function($q) use ($request) {
                $search = "%{$request->search}%";
                $q->whereHas('pet', function($q) use ($search) {
                    $q->where('name', 'like', $search);
                });
            })
            ->latest('requested_date');

        return response()->json([
            'data' => $query->paginate(10)
        ]);
    }

    /**
     * API: Get test types
     */
    public function getTestTypes(Request $request)
    {
        $query = TestType::query();
        
        if ($request->category) {
            $query->where('category', $request->category);
        }
        
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        
        return response()->json([
            'data' => $query->get()
        ]);
    }

    /**
     * API: Get test results
     */
    public function getTestResults(Request $request)
    {
        $query = TestResult::with(['testRequest.pet', 'testRequest.testType'])
            ->when($request->result, function($q) use ($request) {
                $q->where('result', $request->result);
            })
            ->when($request->search, function($q) use ($request) {
                $search = "%{$request->search}%";
                $q->whereHas('testRequest.pet', function($q) use ($search) {
                    $q->where('name', 'like', $search);
                });
            })
            ->latest('completed_at');

        return response()->json([
            'data' => $query->paginate(10)
        ]);
    }

    /**
     * API: Get test result for a specific test request
     */
    public function getTestResult(TestRequest $testRequest)
    {
        $testResult = $testRequest->result;
        
        if (!$testResult) {
            return response()->json([
                'message' => 'No result found for this test request.'
            ], 404);
        }

        return response()->json([
            'data' => $testResult->load('testRequest.testType')
        ]);
    }
}