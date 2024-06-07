<?php

namespace App\Models;

use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Children extends User
{
    use HasFactory;
    use HasUser;

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

    final public function additional_classes():BelongsToMany
    {
        return $this->belongsToMany(AdditionalClass::class, 'add_class_child', 'child_id', 'additional_class_id');
    }

    final public function parrent_relations(): BelongsToMany
    {
        return $this->belongsToMany(Parrent::class, 'child_parent_relations', 'child_id', 'parrent_id');
    }

    final public function visited_educational_events(): BelongsToMany
    {
        return $this->belongsToMany(EducationalEvent::class, 'edu_ev_child', 'child_id', 'educational_event_id');
    }

    final public function cultural_events(): BelongsToMany
    {
        return $this->belongsToMany(CulturalEvent::class, 'cult_ev_child', 'child_id', 'cultural_events');
    }
}
