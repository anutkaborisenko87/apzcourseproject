<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'symbol'
    ];

    final public function children(): HasMany
    {
        return $this->hasMany(Children::class, 'group_id', 'id');
    }

    final public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employees_groups', 'group_id', 'employee_id')->withPivot(['date_start', 'date_finish']);
    }

    final public function educationalPrograms(): BelongsToMany
    {
        return $this->belongsToMany(EducationalProgram::class, 'ed_prog_group', 'group_id', 'ed_prog_id')->withPivot(['date_start', 'date_finish']);
    }
}
