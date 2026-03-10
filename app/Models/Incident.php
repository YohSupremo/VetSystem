<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incident extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'incident_number',
        'incident_date',
        'incident_type',
        'severity',
        'pet_id',
        'affected_user_id',
        'location',
        'cage_id',
        'description',
        'immediate_action_taken',
        'root_cause',
        'corrective_action',
        'status',
        'resolved_date',
        'reported_by',
        'reported_at',
    ];

    protected $casts = [
        'incident_date' => 'datetime',
        'resolved_date' => 'datetime',
        'reported_at' => 'datetime',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function affectedUser()
    {
        return $this->belongsTo(User::class, 'affected_user_id');
    }

    public function cage()
    {
        return $this->belongsTo(Cage::class);
    }

    public function incidentNotes()
    {
        return $this->hasMany(IncidentNote::class);
    }
}
