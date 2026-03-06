<?php

namespace App\Policies;

use App\Models\User;
use App\Models\QrScanLog;

class QrScanLogPolicy
{
    /**
     * Determine whether the user can view any QR scan logs
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the log
     */
    public function view(User $user, QrScanLog $log): bool
    {
        // Admin can view all, users can view their own scans, customers can view scans of their pets
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // User can view their own scans
        if ($user->id === $log->scanned_by) {
            return true;
        }
        
        // Customer can view scans of their own pets
        if ($user->hasRole('customer') && $log->pet_id) {
            $petOwner = $log->pet->owner;
            return $petOwner && $petOwner->user_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create logs
     */
    public function create(User $user): bool
    {
        // Admin, boarding staff, and customers can scan
        return $user->hasRole('admin') || $user->hasRole('boarding') || $user->hasRole('customer');
    }

    /**
     * Determine whether the user can delete the log
     */
    public function delete(User $user, QrScanLog $log): bool
    {
        return $user->hasRole('admin');
    }
}
