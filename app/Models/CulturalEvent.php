<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CulturalEvent extends Model
{
    protected $fillable = [
        'subject',
        'event_date',
        'didactic_materials',
        'event_description',
        'employee_id',
    ];

    final public function organisator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    final public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Children::class, 'cult_ev_child', 'cultural_events', 'child_id')->withPivot('role');
    }



    final public function visitors(): BelongsToMany
    {
        return $this->belongsToMany(Parrent::class, 'cult_ev_visitior', 'cultural_event_id', 'parrent_id')->withPivot('reaction');
    }
}
