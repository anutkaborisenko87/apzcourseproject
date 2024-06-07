<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ParentalEvent extends Model
{
    protected $fillable = [
        'title',
        'object',
        'event_description',
        'employee_id',
    ];

    final public function organisator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    final public function parrents_visitors(): BelongsToMany
    {
        return $this->belongsToMany(Parrent::class, 'parent_ev_parent', 'parental_event_id', 'parrent_id');
    }
}
