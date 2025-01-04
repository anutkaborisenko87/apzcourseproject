<?php

namespace App\Models;

use App\Interfaces\ModelInterfaces\SearchableInterface;
use App\Traits\HasUser;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Children extends Model implements SearchableInterface
{
    use HasFactory;
    use HasUser;
    use Sortable;

    protected $table = 'childrens';
    protected $fillable = [
        'user_id',
        'group_id',
        'mental_helth',
        'birth_certificate',
        'medical_card_number',
        'social_status',
        'enrollment_year',
        'enrollment_date',
        'graduation_year',
        'graduation_date',
    ];

    final public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    final public function parrent_relations(): BelongsToMany
    {
        return $this->belongsToMany(Parrent::class, 'child_parent_relations', 'child_id', 'parrent_id')->withPivot('relations');
    }

    final public function visited_educational_events(): BelongsToMany
    {
        return $this->belongsToMany(EducationalEvent::class, 'edu_ev_child', 'child_id', 'educational_event_id')->withPivot('estimation_mark');
    }

    public static function getSearchableFields(): array
    {
        return  [
            'group',
            'birth_certificate',
            'medical_card_number',
            'social_status',
            'enrollment_year',
            'enrollment_date',
            'graduation_year',
            'graduation_date',
        ];
    }
}
