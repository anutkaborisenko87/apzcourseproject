<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EducationalEvent extends Model
{
    protected $fillable = [
        'subject',
        'event_date',
        'didactic_materials',
        'developed_skills',
        'event_description',
        'employee_id',
        'educational_program_id',
    ];

    final public function teacher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    final public function educational_program(): BelongsTo
    {
        return $this->belongsTo(EducationalProgram::class, 'educational_program_id', 'id');
    }

    final public function children_visitors(): BelongsToMany
    {
        return $this->belongsToMany(Children::class, 'edu_ev_child', 'educational_event_id', 'child_id');
    }
}
