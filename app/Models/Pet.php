<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'registration_number',
        'photo_path',
        'qr_code_path',
        'is_active',
        'deceased_date',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'deceased_date' => 'date',
        'weight' => 'float',
        'is_active' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(PetOwner::class, 'owner_id');
    }

    public function chronicConditions()
    {
        return $this->hasMany(ChronicCondition::class);
    }

    public function petAllergies()
    {
        return $this->hasMany(PetAllergy::class);
    }

    public function surgeries()
    {
        return $this->hasMany(Surgery::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function petVaccinations()
    {
        return $this->hasMany(PetVaccination::class);
    }

    public function cageAssignments()
    {
        return $this->hasMany(CageAssignment::class);
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
        if ($this->photo_path && trim($this->photo_path) !== '') {
            $path = ltrim($this->photo_path, '/');
            return asset($path) . '?v=' . ($this->updated_at ? $this->updated_at->timestamp : time());
        }
        return 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect fill="#f0f0f0" width="200" height="200"/><text x="50%" y="50%" font-size="80" text-anchor="middle" dominant-baseline="middle" fill="#ccc">?</text></svg>');
    }

    public function currentCage()
    {
        return $this->hasOneThrough(
            Cage::class,
            CageAssignment::class,
            'pet_id',
            'id',
            'id',
            'cage_id'
        )->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now());
    }

    protected static function booted()
    {
        static::creating(function ($pet) {
            if (empty($pet->registration_number)) {
                $year = now()->year;
                $prefix = "PET-{$year}-";

                $last = DB::table('pets')
                    ->where('registration_number', 'like', $prefix . '%')
                    ->orderByDesc('id')
                    ->value('registration_number');

                $seq = 1;
                if ($last) {
                    $parts = explode('-', $last);
                    $lastSeq = intval(end($parts));
                    $seq = $lastSeq + 1;
                } else {
                    $count = DB::table('pets')->where('registration_number', 'like', $prefix . '%')->count();
                    if ($count > 0) {
                        $seq = $count + 1;
                    }
                }

                $pet->registration_number = $prefix . str_pad($seq, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
