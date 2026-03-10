<?php

namespace App\Http\Controllers;

use App\Models\QrScanLog;
use App\Models\Cage;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QrScanController extends Controller
{
    /**
     * Log a QR code scan
     * POST /api/qr-scan
     */
    public function store(Request $request)
    {
        $request->validate([
            'scan_type' => 'required|in:cage,pet',
            'cage_id' => 'nullable|exists:cages,id',
            'pet_id' => 'nullable|exists:pets,id',
            'location' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        // Ensure user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();

        // Validate that either cage_id or pet_id is provided
        if (!$request->cage_id && !$request->pet_id) {
            return response()->json(['error' => 'Either cage_id or pet_id must be provided'], 422);
        }

        // Check authorization - customers can only scan their own pets
        if ($user->hasRole('customer') && $request->pet_id) {
            $pet = Pet::findOrFail($request->pet_id);
            $petOwner = $pet->owner;
            if (!$petOwner || $petOwner->user_id !== $user->id) {
                return response()->json(['error' => 'You can only scan your own pets'], 403);
            }
        }

        // Create the scan log
        $scanLog = QrScanLog::safeLog([
            'scan_type' => $request->scan_type,
            'cage_id' => $request->cage_id,
            'pet_id' => $request->pet_id,
            'scanned_by' => $user->id,
            'scan_timestamp' => now(),
            'location' => $request->location,
            'notes' => $request->notes,
        ]);

        if (!$scanLog) {
            return response()->json([
                'message' => 'Scan processed, but logging table is not available yet.',
            ], 202);
        }

        // Load relationships
        $scanLog->load('cage', 'pet', 'scannedBy');

        return response()->json([
            'message' => 'QR scan logged successfully',
            'scan' => $scanLog,
        ], 201);
    }

    /**
     * Get scan logs for a specific cage
     * GET /api/qr-scan/cage/{cageId}
     */
    public function cageLogs($cageId)
    {
        $this->authorize('viewAny', QrScanLog::class);

        $logs = QrScanLog::where('cage_id', $cageId)
            ->with('scannedBy')
            ->orderBy('scan_timestamp', 'desc')
            ->paginate(20);

        return response()->json($logs);
    }

    /**
     * Get scan logs for a specific pet
     * GET /api/qr-scan/pet/{petId}
     */
    public function petLogs($petId)
    {
        $this->authorize('viewAny', QrScanLog::class);

        $logs = QrScanLog::where('pet_id', $petId)
            ->with('scannedBy')
            ->orderBy('scan_timestamp', 'desc')
            ->paginate(20);

        return response()->json($logs);
    }

    /**
     * Get all scan logs for a specific user
     * GET /api/qr-scan/user/{userId}
     */
    public function userLogs($userId)
    {
        $this->authorize('viewAny', QrScanLog::class);

        $logs = QrScanLog::where('scanned_by', $userId)
            ->with('cage', 'pet')
            ->orderBy('scan_timestamp', 'desc')
            ->paginate(20);

        return response()->json($logs);
    }

    /**
     * Get all scan logs
     * GET /api/qr-scan
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', QrScanLog::class);

        $query = QrScanLog::with('cage', 'pet', 'scannedBy');

        // Filter by scan type
        if ($request->scan_type) {
            $query->where('scan_type', $request->scan_type);
        }

        // Filter by date range
        if ($request->from_date) {
            $query->whereDate('scan_timestamp', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('scan_timestamp', '<=', $request->to_date);
        }

        // Filter by scanned_by (user)
        if ($request->scanned_by) {
            $query->where('scanned_by', $request->scanned_by);
        }

        $logs = $query->orderBy('scan_timestamp', 'desc')->paginate(20);

        return response()->json($logs);
    }
}
