<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pet;
use App\Models\PetOwner;
use App\Models\PetVaccination;
use App\Models\Appointment;
use App\Models\InventoryItem;
use App\Models\ShoppingCart;
use App\Models\Notification;
use App\Mail\VaccinationReminderMail;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get the authenticated user from session (matching existing login system)
        $username = session('username');
        if (!$username) {
            return redirect('/login')->with('error', 'Please login first');
        }
        
        $user = User::where('username', $username)->first();
        if (!$user || ($user->role !== 'pet_owner' && $user->role !== 'registered_user')) {
            return redirect('/login')->with('error', 'Access denied');
        }

        // Auto-check vaccination reminders for this customer
        $this->checkVaccinationReminders($user);
        
        // Get customer's pets
        $pets = Pet::with(['prescriptions' => function ($query) {
                $query->latest('created_at');
            }])
            ->where('owner_id', function($query) use ($user) {
                $query->select('id')
                      ->from('pet_owners')
                      ->where('user_id', $user->id);
            })
            ->get();
        
        // Get upcoming appointments (including today and cancelled ones)
        $upcomingAppointments = Appointment::whereIn('pet_id', $pets->pluck('id'))
            ->where('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed', 'scheduled', 'in_progress', 'cancelled'])
            ->orderBy('appointment_date', 'asc')
            ->take(5)
            ->get();
        
        // Get recent appointments
        $recentAppointments = Appointment::whereIn('pet_id', $pets->pluck('id'))
            ->where('appointment_date', '<=', now())
            ->where('status', 'completed')
            ->orderBy('appointment_date', 'desc')
            ->take(3)
            ->get();
        
        // Get pet count and statistics
        $petCount = $pets->count();
        $upcomingCount = $upcomingAppointments->count();

        // Get user's cart information
        $cart = ShoppingCart::getOrCreateForUser($user->id);

        $notifications = Notification::query()
            ->forUser($user->id)
            ->visibleToRole($user->role)
            ->latest()
            ->take(8)
            ->get();

        $unreadNotificationCount = Notification::query()
            ->forUser($user->id)
            ->visibleToRole($user->role)
            ->unread()
            ->count();
        
        return view('customer.dashboard', compact(
            'user',
            'pets', 
            'upcomingAppointments',
            'recentAppointments',
            'petCount',
            'upcomingCount',
            'cart',
            'notifications',
            'unreadNotificationCount'
        ));
    }

    /**
     * Auto-check and send vaccination reminders for the customer's pets.
     * Sends notifications for vaccinations due within 2 days that haven't been reminded yet.
     */
    private function checkVaccinationReminders(User $user): void
    {
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            return;
        }

        $petIds = $petOwner->pets()->pluck('id');
        if ($petIds->isEmpty()) {
            return;
        }

        $today = Carbon::today();
        $threshold = $today->copy()->addDays(2);

        // Find vaccinations due within 2 days that haven't been reminded
        $upcomingVaccinations = PetVaccination::with('pet')
            ->whereIn('pet_id', $petIds)
            ->whereNotNull('next_due_date')
            ->whereBetween('next_due_date', [$today, $threshold])
            ->where(function ($query) {
                $query->whereNull('reminder_sent')
                      ->orWhere('reminder_sent', 0);
            })
            ->get();

        if ($upcomingVaccinations->isEmpty()) {
            return;
        }

        $notificationService = app(NotificationService::class);

        foreach ($upcomingVaccinations as $vaccination) {
            $petName = $vaccination->pet->name ?? 'your pet';
            $vaccineName = $vaccination->vaccine_name ?? 'Vaccination';
            $dueDate = Carbon::parse($vaccination->next_due_date)->format('M d, Y');

            // In-app notification
            $notificationService->send(
                $user,
                Notification::TYPE_VACCINATION,
                'Vaccination Due Soon',
                "{$vaccineName} for {$petName} is due on {$dueDate}. Please schedule an appointment.",
                [
                    'reference_type' => 'vaccination',
                    'reference_id' => $vaccination->id,
                    'priority' => Notification::PRIORITY_HIGH,
                ]
            );

            // Email notification
            try {
                if ($user->email) {
                    Mail::to($user->email)->send(new VaccinationReminderMail($vaccination));
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to send vaccination reminder email', [
                    'user_id' => $user->id,
                    'vaccination_id' => $vaccination->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $vaccination->update(['reminder_sent' => 1]);
        }
    }
}
