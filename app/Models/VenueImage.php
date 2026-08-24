<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueImage extends Model
{
    protected $fillable = ['venue_id', 'path', 'sort_order'];

    // Ảnh thuộc về 1 khu sân
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}
