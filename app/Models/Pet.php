<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Pet extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'species',
        'breed',
        'birth_date',
        'gender',
        'color',
        'weight',
        'microchip_number',
        'photo_path',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'weight' => 'float',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(PetOwner::class);
    }

 

    public function getAgeAttribute()
    {
        if (!$this->birth_date) {
            return 'Unknown';
        }
        return $this->birth_date->age . ' years';
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path && !empty(trim($this->photo_path))) {
            // Normalize path (remove leading slashes)
            // The path is already stored as 'pets/filename.jpg' by store() method
            $path = ltrim($this->photo_path, '/');
            
            // Try public/storage symlink path first (standard Laravel)
            // This is the correct path after running: php artisan storage:link
            $publicStoragePath = public_path('storage/' . $path);
            if (file_exists($publicStoragePath)) {
                return asset('storage/' . $path);
            }
            
            // Try storage/app/public path (if symlink doesn't exist yet)
            $storagePath = storage_path('app/public/' . $path);
            if (file_exists($storagePath)) {
                // File exists but symlink doesn't - return URL that should work
                // Note: You need to run 'php artisan storage:link' for this to work via web
                return asset('storage/' . $path);
            }
            
            // If path exists in DB but file not found, still return the asset URL
            // This allows the image to load once symlink is created
            return asset('storage/' . $path);
        }
        
        // Default fallback - return a placeholder SVG
        return 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect fill="#f0f0f0" width="200" height="200"/><text x="50%" y="50%" font-size="80" text-anchor="middle" dominant-baseline="middle" fill="#ccc">🐾</text></svg>');
    }

    public function surgeries(): HasMany
    {
        return $this->hasMany(Surgery::class);
    }

    public function vaccinations(): HasMany
    {
        return $this->hasMany(Vaccination::class);
    }

       public function cageAssignments(): HasMany
    {
        return $this->hasMany(CageAssignment::class);
    }

    public function feedingSchedules(): HasMany {
        return $this->hasMany(FeedingSchedule::class);
    }

    public function medicationIntructions(): HasMany {
        return $this->hasMany(MedicationInstruction::class);
    }
    public function currentCage(): HasOneThrough {
        return $this->hasOneThrough(
            Cage::class,          // The final model
            CageAssignment::class, // The intermediate model
            'pet_id',             // FK on CageAssignment
            'id',                 // FK on Cage
            'id',                 // Local key on Pet
            'cage_id'             // Local key on CageAssignment
        )->whereDate('start_date', '<=', now())
         ->whereDate('end_date', '>=', now());
    }

     public function prescriptions(): HasMany {
        return $this->hasMany(Prescription::class);
    }
}
