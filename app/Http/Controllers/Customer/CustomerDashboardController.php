<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pet;
use App\Models\Appointment;
use App\Models\InventoryItem;
use App\Models\ShoppingCart;
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
        
        // Get customer's pets
        $pets = Pet::where('owner_id', function($query) use ($user) {
            $query->select('id')
                  ->from('pet_owners')
                  ->where('user_id', $user->id);
        })->get();
        
        // Get upcoming appointments (including today and cancelled ones)
        $upcomingAppointments = Appointment::whereIn('pet_id', $pets->pluck('id'))
            ->where('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'scheduled', 'in_progress', 'cancelled'])
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
        
        return view('customer.dashboard', compact(
            'user',
            'pets', 
            'upcomingAppointments',
            'recentAppointments',
            'petCount',
            'upcomingCount',
            'cart'
        ));
    }
}
