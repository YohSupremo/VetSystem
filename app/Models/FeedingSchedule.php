<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedingSchedule extends Model
{
      protected $fillable = ['pet_id', 'schedule', 'notes'];
    public function pet(): BelongsTo {
        return $this->belongsTo(Pet::class);
    }

}
