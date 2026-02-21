<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create notification settings for all active users if they don't have one
        $users = User::all();

        foreach ($users as $user) {
            NotificationSetting::updateOrCreate(
                ['user_id' => $user->id],
                NotificationSetting::getDefaultSettings($user->id)
            );
        }

        // Create sample notifications for admin users
        $adminUsers = User::where('role', 'admin')->get();

        foreach ($adminUsers as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'appointment_reminder',
                'title' => 'New Appointment Scheduled',
                'message' => 'Dr. Smith has scheduled an appointment for Max on 2026-02-25 at 2:00 PM',
                'icon' => 'calendar-alt',
                'priority' => Notification::PRIORITY_NORMAL,
                'method' => 'email',
                'status' => 'sent',
                'is_read' => false,
                'scheduled_for' => now(),
                'sent_at' => now(),
                'action_url' => route('admin.appointments.index'),
                'reference_type' => 'appointment',
                'reference_id' => 1,
            ]);

            Notification::create([
                'user_id' => $admin->id,
                'type' => 'payment_due',
                'title' => 'Payment Due',
                'message' => 'Invoice #INV-2026-001 is due for payment',
                'icon' => 'credit-card',
                'priority' => Notification::PRIORITY_HIGH,
                'method' => 'email',
                'status' => 'sent',
                'is_read' => false,
                'scheduled_for' => now(),
                'sent_at' => now(),
                'action_url' => route('admin.billing.invoices.index'),
                'reference_type' => 'invoice',
                'reference_id' => 1,
            ]);

            Notification::create([
                'user_id' => $admin->id,
                'type' => 'low_stock',
                'title' => 'Low Stock Alert',
                'message' => 'Amoxicillin 250mg is running low (5 units remaining)',
                'icon' => 'boxes',
                'priority' => Notification::PRIORITY_NORMAL,
                'method' => 'email',
                'status' => 'sent',
                'is_read' => true,
                'scheduled_for' => now()->subHours(2),
                'read_at' => now()->subHours(2),
                'sent_at' => now()->subHours(2),
                'action_url' => route('admin.inventory.index'),
                'reference_type' => 'inventory',
                'reference_id' => 1,
            ]);
        }

        // Create sample notifications for veterinarians
        $veterinarians = User::where('role', 'veterinarian')->get();

        foreach ($veterinarians as $vet) {
            Notification::create([
                'user_id' => $vet->id,
                'type' => 'lab_result',
                'title' => 'New Lab Results',
                'message' => 'Lab results for Buddy are now available',
                'icon' => 'flask',
                'priority' => Notification::PRIORITY_NORMAL,
                'method' => 'email',
                'status' => 'sent',
                'is_read' => false,
                'scheduled_for' => now(),
                'sent_at' => now(),
                'action_url' => route('veterinarian.laboratory.index'),
                'reference_type' => 'lab_test',
                'reference_id' => 1,
            ]);

            Notification::create([
                'user_id' => $vet->id,
                'type' => 'vaccination_due',
                'title' => 'Vaccination Due Soon',
                'message' => 'Max\'s rabies vaccination is due in 7 days',
                'icon' => 'syringe',
                'priority' => Notification::PRIORITY_NORMAL,
                'method' => 'email',
                'status' => 'sent',
                'is_read' => false,
                'scheduled_for' => now(),
                'sent_at' => now(),
                'action_url' => route('veterinarian.vaccinations.index'),
                'reference_type' => 'vaccination',
                'reference_id' => 1,
            ]);
        }

        $this->command->info('Notification seeder completed successfully!');
    }
}
