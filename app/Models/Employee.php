<?php

namespace App\Models;

use App\Interfaces\ModelInterfaces\SearchableInterface;
use App\Traits\HasUser;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model implements SearchableInterface
{
    use HasUser;
    use HasFactory;
    use Sortable;

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

    final public function educational_events(): HasMany
    {
        return $this->hasMany(EducationalEvent::class, 'employee_id', 'id');
    }

    public static function getSearchableFields(): array
    {
        return [
            'phone',
            'contract_number',
            'passport_data',
            'medical_card_number',
            'employment_date',
            'date_dismissal',
        ];
    }
}
