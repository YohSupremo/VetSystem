<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\StaffSchedule;
use Illuminate\Http\Request;

class StaffScheduleController extends BaseController
{
    /**
     * Display staff schedules
     */
    public function index()
    {
        $staff = User::whereIn('role', ['veterinarian', 'staff', 'reception', 'groomer', 'boarding', 'pharmacy'])
            ->with('staffSchedules')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $shifts = ['morning' => '9:00 AM - 5:00 PM', 'night' => '5:00 PM - 12:00 AM'];

        return view('admin.staff-schedules.index', compact('staff', 'daysOfWeek', 'shifts'));
    }

    /**
     * Show form to edit staff member's schedule
     */
    public function edit($userId)
    {
        $user = User::with('staffSchedules')->findOrFail($userId);
        
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $shifts = ['morning', 'night'];

        // Get existing schedules grouped by day and shift
        $existingSchedules = $user->staffSchedules->keyBy(function ($schedule) {
            return $schedule->day_of_week . '_' . $schedule->shift;
        });

        return view('admin.staff-schedules.edit', compact('user', 'daysOfWeek', 'shifts', 'existingSchedules'));
    }

    /**
     * Update staff member's schedule
     */
    public function update(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'schedules' => 'nullable|array',
            'schedules.*' => 'in:Monday_morning,Monday_night,Tuesday_morning,Tuesday_night,Wednesday_morning,Wednesday_night,Thursday_morning,Thursday_night,Friday_morning,Friday_night,Saturday_morning,Saturday_night,Sunday_morning,Sunday_night',
        ]);

        $requestedSchedules = $validated['schedules'] ?? [];

        foreach ($requestedSchedules as $schedule) {
            [$day, $shift] = explode('_', $schedule);

            if (!$this->canAssignShift($user, $day, $shift)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'schedules' => $this->getShiftLimitErrorMessage($user, $day, $shift),
                    ]);
            }
        }

        // Delete all existing schedules for this user
        StaffSchedule::where('user_id', $userId)->delete();

        // Create new schedules
        foreach ($requestedSchedules as $schedule) {
            [$day, $shift] = explode('_', $schedule);

            StaffSchedule::create([
                'user_id' => $userId,
                'day_of_week' => $day,
                'shift' => $shift,
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.staff-schedules.index')
            ->with('success', 'Staff schedule updated successfully.');
    }

    /**
     * Quick toggle schedule (AJAX)
     */
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'shift' => 'required|in:morning,night',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $schedule = StaffSchedule::where('user_id', $validated['user_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where('shift', $validated['shift'])
            ->first();

        if ($schedule) {
            // Delete existing schedule
            $schedule->delete();
            $action = 'removed';
        } else {
            if (!$this->canAssignShift($user, $validated['day_of_week'], $validated['shift'])) {
                return response()->json([
                    'success' => false,
                    'message' => $this->getShiftLimitErrorMessage($user, $validated['day_of_week'], $validated['shift']),
                ], 422);
            }

            // Create new schedule
            StaffSchedule::create([
                'user_id' => $validated['user_id'],
                'day_of_week' => $validated['day_of_week'],
                'shift' => $validated['shift'],
                'is_active' => true,
            ]);
            $action = 'added';
        }

        return response()->json([
            'success' => true,
            'action' => $action,
            'message' => 'Schedule updated successfully.'
        ]);
    }

    private function canAssignShift(User $user, string $dayOfWeek, string $shift): bool
    {
        $limit = $this->getShiftLimitByRole($user->role);

        $count = StaffSchedule::where('day_of_week', $dayOfWeek)
            ->where('shift', $shift)
            ->whereHas('user', function ($query) use ($user) {
                $query->where('role', $user->role);
            })
            ->where('user_id', '!=', $user->id)
            ->count();

        return $count < $limit;
    }

    private function getShiftLimitByRole(?string $role): int
    {
        if ($role === 'reception') {
            return 1;
        }

        return 3;
    }

    private function getShiftLimitErrorMessage(User $user, string $dayOfWeek, string $shift): string
    {
        $roleLabel = $user->role ? ucfirst($user->role) : 'Staff';
        $shiftLabel = $shift === 'morning' ? 'morning' : 'night';
        $limit = $this->getShiftLimitByRole($user->role);

        return "Limit reached: only {$limit} {$roleLabel} allowed for {$dayOfWeek} {$shiftLabel} shift.";
    }
}
