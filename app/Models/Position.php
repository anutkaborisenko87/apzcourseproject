<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{

    protected $table = 'positions';
    protected $fillable = [
        'position_title'
    ];
    final public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'position_id', 'id');
    }
}
