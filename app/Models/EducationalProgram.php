<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationalProgram extends Model
{
    protected $fillable = [
        'program_number',
        'age_restrictions',
        'approval_date',
        'employee_id',
    ];

    final public function author(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    final public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'ed_prog_group', 'ed_prog_id', 'group_id');
    }

    final public function additionalClasses(): HasMany
    {
        return $this->hasMany(AdditionalClass::class, 'educational_program_id', 'id');
    }

    final public function educational_events(): HasMany
    {
        return $this->hasMany(EducationalEvent::class, 'educational_program_id', 'id');
    }
}
