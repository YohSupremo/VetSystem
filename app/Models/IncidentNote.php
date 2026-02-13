<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentNote extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'incident_id',
        'note',
        'added_by',
        'added_at',
    ];

    protected $casts = [
        'added_at' => 'datetime',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
