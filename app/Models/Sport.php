<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    protected $fillable = ['name', 'icon', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    // 1 môn thể thao có nhiều sân con
    public function courts()
    {
        return $this->hasMany(Court::class);
    }
}
