<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EducationalEvent extends Model
{
    use HasFactory;
    protected $fillable = [
        'subject',
        'event_date',
        'didactic_materials',
        'developed_skills',
        'event_description',
        'employee_id',
    ];

    final public function teacher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    final public function children_visitors(): BelongsToMany
    {
        return $this->belongsToMany(Children::class, 'edu_ev_child', 'educational_event_id', 'child_id')->withPivot('estimation_mark');
    }
}
