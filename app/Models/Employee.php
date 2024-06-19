<?php

namespace App\Models;

use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends User
{
    use HasUser;
    use HasFactory;
    protected $table = 'employees';
    protected $fillable = [
        'user_id',
        'position_id',
        'phone',
        'contract_number',
        'passport_data',
        'bank_account',
        'bank_title',
        'EDRPOU_bank_code',
        'code_IBAN',
        'medical_card_number',
        'employment_date',
        'date_dismissal',
    ];

    final public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id', 'id');
    }

    final public function qualifyingEvents(): BelongsToMany
    {
        return $this->belongsToMany(QualifyingEvent::class, 'emp_qualif_evs', 'employee_id', 'qualif_ev_id');
    }

    final public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'employees_groups', 'employee_id', 'group_id')->withPivot(['date_start', 'date_finish']);
    }

    final public function educational_programs(): HasMany
    {
        return $this->hasMany(EducationalProgram::class, 'employee_id', 'id');
    }

    final public function additional_classes(): HasMany
    {
        return $this->hasMany(AdditionalClass::class, 'employee_id', 'id');
    }

    final public function parrental_events(): HasMany
    {
        return $this->hasMany(ParentalEvent::class, 'employee_id', 'id');
    }

    final public function educational_events(): HasMany
    {
        return $this->hasMany(EducationalEvent::class, 'employee_id', 'id');
    }

    final public function cultural_events(): HasMany
    {
        return $this->hasMany(CulturalEvent::class, 'employee_id', 'id');
    }
}
