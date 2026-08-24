<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourtClosure extends Model
{
    protected $fillable = ['court_id', 'date', 'start_time', 'end_time', 'reason'];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    // Thuộc về 1 sân con
    public function court()
    {
        return $this->belongsTo(Court::class);
    }
}
