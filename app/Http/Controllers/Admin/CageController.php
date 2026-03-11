<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cage;
use App\Models\CageAssignment;
use App\Models\QrScanLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CageController extends Controller
{
    private function isLoopbackHost(string $host): bool
    {
        return in_array(strtolower($host), ['127.0.0.1', 'localhost', '::1'], true);
    }

    private function detectLanIp(): ?string
    {
        // On Windows, parse ipconfig to find the adapter with a real LAN gateway
        if (PHP_OS_FAMILY === 'Windows') {
            $output = shell_exec('ipconfig');
            if ($output) {
                $sections = preg_split('/\r?\n(?=\S)/', $output);
                foreach ($sections as $section) {
                    if (preg_match('/Default Gateway[.\s]*:\s*(\d+\.\d+\.\d+\.\d+)/i', $section, $gw)
                        && preg_match('/IPv4 Address[.\s]*:\s*(\d+\.\d+\.\d+\.\d+)/i', $section, $ip)) {
                        $ipParts = explode('.', $ip[1]);
                        $gwParts = explode('.', $gw[1]);
                        // Gateway must share the same first 3 octets (same /24 subnet)
                        if ($ipParts[0] === $gwParts[0] && $ipParts[1] === $gwParts[1] && $ipParts[2] === $gwParts[2]) {
                            return $ip[1];
                        }
                    }
                }
            }
        }

        // Fallback: pick from hostname lookup
        $candidates = gethostbynamel(gethostname()) ?: [];

        foreach ($candidates as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
                && !str_starts_with($ip, '127.')
                && preg_match('/^(10\.|192\.168\.|172\.(1[6-9]|2[0-9]|3[0-1])\.)/', $ip)) {
                return $ip;
            }
        }

        return null;
    }

    private function resolveScanBaseUrl(): string
    {
        $request = request();
        $scheme = $request->getScheme();
        $host = $request->getHost();
        $port = (int) $request->getPort();

        if ($this->isLoopbackHost($host)) {
            // Use the actual server IP the request arrived on (works with --host=0.0.0.0)
            $serverAddr = $request->server('SERVER_ADDR', '');
            if ($serverAddr !== '' && !$this->isLoopbackHost($serverAddr)) {
                $host = $serverAddr;
            } else {
                $detectedIp = $this->detectLanIp();
                if ($detectedIp) {
                    $host = $detectedIp;
                }
            }
        }

        $base = $scheme . '://' . $host;
        if (!in_array($port, [80, 443], true)) {
            $base .= ':' . $port;
        }

        return rtrim($base, '/');
    }

    private function sharedScanUrl(string $cageCode): string
    {
        $baseUrl = $this->resolveScanBaseUrl();
        $path = route('admin.cages.scan', ['code' => $cageCode], false);

        return $baseUrl . $path;
    }

    private function activeAssignmentQuery(int $cageId)
    {
        $now = now();

        return CageAssignment::where('cage_id', $cageId)
            ->where(function ($query) use ($now) {
                $query->whereNull('check_in_time')
                    ->whereDate('start_date', '<=', $now->toDateString())
                    ->orWhere('check_in_time', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('check_out_time')
                    ->whereDate('end_date', '>=', $now->toDateString())
                    ->orWhere('check_out_time', '>', $now);
            });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Sync all cages to ensure their status matches actual assignments
        $allCages = Cage::all();
        foreach ($allCages as $cage) {
            $cage->syncStatus();
        }
        
        $cages = Cage::orderBy('cage_code')->paginate(10);
        $scanBaseUrl = $this->resolveScanBaseUrl();

        return view('admin.cages.index', compact('cages', 'scanBaseUrl'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cage = Cage::findOrFail($id);
        
        // Sync cage status based on active assignments
        $cage->syncStatus();
        
        // Find current assignment if any
        $assignment = $this->activeAssignmentQuery($cage->id)
            ->with(['pet.owner', 'pet.medicalRecords'])
            ->latest()
            ->first();

        // Use shared app base URL (APP_URL) so phone scans resolve on LAN.
        $scanUrl = $this->sharedScanUrl($cage->cage_code);

        return view('admin.cages.show', compact('cage', 'assignment', 'scanUrl'));
    }

    /**
     * Scan the QR code and display contents
     */
    public function scan(string $code)
    {
        $cage = Cage::where('cage_code', $code)->firstOrFail();

        // Sync cage status based on active assignments
        $cage->syncStatus();

        // Find active assignment (only if currently active)
        // Logic: start_date <= today AND end_date >= today
        $assignment = $this->activeAssignmentQuery($cage->id)
            ->with(['pet.owner', 'pet.medicalRecords' => function($query) {
                $query->latest()->limit(5);
            }])
            ->latest()
            ->first();

        // Log the scan
        QrScanLog::safeLog([
            'scan_type' => 'cage',
            'cage_id' => $cage->id,
            'pet_id' => $assignment?->pet_id,
            'scanned_by' => Auth::id(),
            'scan_timestamp' => now(),
        ]);

        return view('admin.cages.scan', compact('cage', 'assignment'));
    }

    /**
     * Release current pet from cage.
     */
    public function release(string $id)
    {
        $cage = Cage::findOrFail($id);

        $assignment = $this->activeAssignmentQuery($cage->id)->latest()->first();

        if (!$assignment) {
            return redirect()->route('admin.cages.index')
                ->with('warning', 'No active cage assignment to release.');
        }

        $assignment->update([
            'check_out_time' => now(),
        ]);

        $cage->syncStatus();

        return redirect()->route('admin.cages.index')
            ->with('success', 'Pet released successfully. Cage is now available.');
    }
}
