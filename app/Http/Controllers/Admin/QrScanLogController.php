<?php

namespace App\Http\Controllers\Admin;

use App\Models\QrScanLog;
use App\Models\User;
use Illuminate\Http\Request;

class QrScanLogController extends BaseController
{
    /**
     * Display QR scan logs history for admins.
     */
    public function index(Request $request)
    {
        $query = QrScanLog::with(['pet.owner.user', 'cage', 'scannedBy'])
            ->orderByDesc('scan_timestamp');

        if ($request->filled('scan_type')) {
            $query->where('scan_type', $request->scan_type);
        }

        if ($request->filled('scanned_by')) {
            $query->where('scanned_by', $request->scanned_by);
        }

        if ($request->filled('pet_name')) {
            $petName = $request->pet_name;
            $query->whereHas('pet', function ($builder) use ($petName) {
                $builder->where('name', 'like', "%{$petName}%");
            });
        }

        if ($request->filled('cage_code')) {
            $cageCode = $request->cage_code;
            $query->whereHas('cage', function ($builder) use ($cageCode) {
                $builder->where('cage_code', 'like', "%{$cageCode}%")
                    ->orWhere('name', 'like', "%{$cageCode}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('scan_timestamp', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('scan_timestamp', '<=', $request->to_date);
        }

        $logs = $query->paginate(20)->appends($request->query());
        $users = User::orderBy('first_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name', 'username']);

        return view('admin.qr-scan-logs.index', compact('logs', 'users'));
    }
}
