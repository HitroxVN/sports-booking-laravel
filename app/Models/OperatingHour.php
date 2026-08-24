<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatingHour extends Model
{
    public $timestamps = false; // không cần created_at/updated_at

    protected $fillable = ['venue_id', 'day_of_week', 'open_time', 'close_time', 'is_closed'];

    protected function casts(): array
    {
        return ['is_closed' => 'boolean'];
    }

    // Thuộc về 1 khu sân
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}
