<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\TestRequest;
use App\Models\TestType;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaboratoryController extends Controller
{
    /**
     * Display the laboratory dashboard
     */
    public function index()
    {
        return view('admin.laboratory.index');
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