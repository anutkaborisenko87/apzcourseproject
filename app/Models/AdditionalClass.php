<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdditionalClass extends Model
{
    protected $fillable = [
        'title',
        'orientation',
        'age_restrictions',
        'limit_visitors',
        'employee_id',
        'educational_program_id',
    ];

    final public function educational_program(): BelongsTo
    {
        return $this->belongsTo(EducationalProgram::class, 'educational_program_id', 'id');
    }

    final public function teacher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    final public function visitors():BelongsToMany
    {
        return $this->belongsToMany(Children::class, 'add_class_child', 'additional_class_id', 'child_id');
    }
}
