<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Please log in first to access this module.');
        }

        $user = Auth::user();
        $routeName = $request->route()->getName();

        // Define allowed routes for each role
        $rolePermissions = [
            'admin' => ['*'], // Admin can access everything
            'pharmacy' => [
                'admin.dashboard',
                'admin.pharmacy.*',
                'admin.prescriptions.*', // view/dispense only
                'admin.inventory.*',
                'admin.orders.*',
                'admin.pharmacy.notifications.*',
                'admin.pharmacy.unread-count',
                'admin.reports.inventory',
                'admin.reports.inventory-report',
            ],
            'reception' => [
                'admin.dashboard',
                'admin.appointments.*',
                'admin.pets.*',
                'admin.pet-owners.*',
                'admin.queue.*',
                'admin.billing.*',
                'admin.reception.notifications.*',
                'admin.reception.unread-count',
                'admin.reports.financial',
                'admin.reports.cancelled-invoices',
                'admin.reports.client',
                'admin.reports.appointment',
            ],
            'staff' => [
                'admin.dashboard',
                'admin.appointments.*',
                'admin.pets.*',
                'admin.pet-owners.*',
                'admin.boarding.*',
                'admin.cages.*',
                'admin.grooming.*',
                'admin.queue.*',
                'admin.staff.notifications.*',
                'admin.staff.unread-count',
            ],
            'boarding' => [
                'admin.dashboard',
                'admin.pets.*',
                'admin.pet-owners.*',
                'admin.boarding.*',
                'admin.cages.*',
                'admin.queue.*',
                'admin.boarding.notifications.*',
                'admin.boarding.unread-count',
            ],
            'groomer' => [
                'admin.dashboard',
                'admin.appointments.*', // grooming appointments only
                'admin.pets.*',
                'admin.pet-owners.*',
                'admin.grooming.*',
                'admin.grooming-services.*',
                'admin.queue.*',
                'admin.grooming.notifications.*',
                'admin.grooming.unread-count',
            ],
            'veterinarian' => [
                'admin.dashboard',
                'admin.appointments.*',
                'admin.pets.*',
                'admin.pet-owners.*',
                'admin.queue.*',
                'admin.medical-records.*',
                'admin.vaccinations.*',
                'admin.prescriptions.*',
                'admin.surgeries.*',
                'admin.laboratory.*',
                'admin.reports.medical',
                'admin.reports.medical-report',
                'admin.notifications.*',
            ],
        ];

        // Check if user role has permissions
        if (!isset($rolePermissions[$user->role])) {
            return $this->redirectToDefaultPage($user->role, 'You are not authorized to access admin modules.');
        }

        $allowedRoutes = $rolePermissions[$user->role];

        // If role has wildcard permission, allow
        if (in_array('*', $allowedRoutes)) {
            return $next($request);
        }

        // Check if current route matches allowed patterns
        foreach ($allowedRoutes as $allowedRoute) {
            if (str_contains($allowedRoute, '*')) {
                // Wildcard pattern
                $pattern = str_replace('*', '.*', $allowedRoute);
                if (preg_match('/^' . $pattern . '$/', $routeName)) {
                    return $next($request);
                }
            } elseif ($routeName === $allowedRoute) {
                return $next($request);
            }
        }

        // If not allowed, redirect to default page
        return $this->redirectToDefaultPage($user->role, 'You are not authorized to access this page.');
    }

    /**
     * Redirect user to their default allowed page based on role
     */
    private function redirectToDefaultPage($role, $message = null)
    {
        switch ($role) {
            case 'veterinarian':
                $redirect = redirect()->route('admin.vaccinations.index');
                break;
            case 'pharmacy':
                $redirect = redirect()->route('admin.inventory.index');
                break;
            case 'reception':
                $redirect = redirect()->route('admin.billing.index');
                break;
            case 'boarding':
            case 'groomer':
                $redirect = redirect()->route('admin.appointments.index');
                break;
            case 'pet_owner':
            case 'registered_user':
            case 'customer':
                $redirect = redirect()->route('customer.dashboard');
                break;
            case 'admin':
                $redirect = redirect()->route('admin.dashboard');
                break;
            default:
                $redirect = redirect()->route('login');
                break;
        }

        return $message ? $redirect->with('error', $message) : $redirect;
    }
}