<?php

namespace App\Http\Controllers\Admin;

use App\Models\ClinicSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends BaseController
{
    /**
     * Display a listing of settings.
     */
    public function index()
    {
        $clinicSettings = ClinicSetting::query()->first()
            ?? new ClinicSetting(ClinicSetting::defaults());

        return view('admin.settings.index', compact('clinicSettings'));
    }

    /**
     * Show the form for creating a new setting.
     */
    public function create()
    {
        return view('admin.settings.create');
    }

    /**
     * Store a newly created setting in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'clinic_name' => 'required|string|max:150',
            'clinic_phone' => 'nullable|string|max:30',
            'clinic_email' => 'nullable|email|max:150',
            'clinic_address' => 'nullable|string',
            'timezone' => 'required|timezone',
            'currency_code' => 'required|string|size:3',
            'invoice_prefix' => 'required|string|max:10',
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'morning_shift_start' => 'required|date_format:H:i',
            'morning_shift_end' => 'required|date_format:H:i|after:morning_shift_start',
            'night_shift_start' => 'required|date_format:H:i',
            'night_shift_end' => 'required|date_format:H:i',
        ]);

        $payload = [
            'clinic_name' => $validated['clinic_name'],
            'clinic_phone' => $validated['clinic_phone'] ?? null,
            'clinic_email' => $validated['clinic_email'] ?? null,
            'clinic_address' => $validated['clinic_address'] ?? null,
            'timezone' => $validated['timezone'],
            'currency_code' => strtoupper($validated['currency_code']),
            'invoice_prefix' => strtoupper($validated['invoice_prefix']),
            'default_tax_rate' => $validated['default_tax_rate'],
            'morning_shift_start' => $validated['morning_shift_start'],
            'morning_shift_end' => $validated['morning_shift_end'],
            'night_shift_start' => $validated['night_shift_start'],
            'night_shift_end' => $validated['night_shift_end'],
            'updated_by' => Auth::id(),
        ];

        $current = ClinicSetting::query()->first();

        if ($current) {
            $current->update($payload);
        } else {
            ClinicSetting::create($payload);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Clinic settings updated successfully.');
    }

    /**
     * Display the specified setting.
     */
    public function show($id)
    {
        return view('admin.settings.show');
    }

    /**
     * Show the form for editing the specified setting.
     */
    public function edit($id)
    {
        return view('admin.settings.edit');
    }

    /**
     * Update the specified setting in storage.
     */
    public function update(Request $request, $id)
    {
        return $this->store($request);
    }

    /**
     * Remove the specified setting from storage.
     */
    public function destroy($id)
    {
        // Placeholder for settings delete logic
        return redirect()->route('admin.settings.index')->with('success', 'Setting deleted successfully.');
    }
}
