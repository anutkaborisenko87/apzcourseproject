<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QualifyingEvent extends Model
{
    use HasFactory;
    protected $fillable = [
        'qualifying_event_title',
        'qualifying_event_description',
        'date_begining',
        'date_finish',
    ];

    final public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'emp_qualif_evs', 'qualif_ev_id', 'employee_id');
    }
}
