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
            return redirect('/login');
        }

        $user = Auth::user();
        $routeName = $request->route()->getName();

        // Define allowed routes for each role
        $rolePermissions = [
            'admin' => ['*'], // Admin can access everything
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
            ],
            'pharmacy' => [
                'admin.dashboard',
                'admin.pharmacy.*',
                'admin.prescriptions.*', // view/dispense only
                'admin.inventory.*',
                'admin.orders.*',
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
            ],
            'boarding' => [
                'admin.dashboard',
                'admin.pets.*',
                'admin.pet-owners.*',
                'admin.boarding.*',
                'admin.cages.*',
                'admin.queue.*',
            ],
            'groomer' => [
                'admin.dashboard',
                'admin.appointments.*', // grooming appointments only
                'admin.pets.*',
                'admin.pet-owners.*',
                'admin.grooming.*',
                'admin.grooming-services.*',
                'admin.queue.*',
            ],
        ];

        // Check if user role has permissions
        if (!isset($rolePermissions[$user->role])) {
            return redirect('/login');
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
        return $this->redirectToDefaultPage($user->role);
    }

    /**
     * Redirect user to their default allowed page based on role
     */
    private function redirectToDefaultPage($role)
    {
        switch ($role) {
            case 'veterinarian':
                return redirect()->route('admin.vaccinations.index');
            case 'pharmacy':
                return redirect()->route('admin.inventory.index');
            case 'reception':
                return redirect()->route('admin.billing.index');
            case 'boarding':
            case 'groomer':
                return redirect()->route('admin.appointments.index');
            case 'admin':
            default:
                return redirect()->route('admin.dashboard');
        }
    }
}