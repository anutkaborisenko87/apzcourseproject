<?php

namespace App\Models;

use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Parrent extends User
{
    use HasUser;

    protected $table = 'parrents';
    protected $fillable = [
        'user_id',
        'phone',
        'work_place',
        'passport_data',
        'marital_status'
    ];

    final public function children_relations(): BelongsToMany
    {
        return $this->belongsToMany(Children::class, 'child_parent_relations', 'parrent_id', 'child_id')->withPivot('relations');
    }

    final public function visited_parental_events(): BelongsToMany
    {
        return $this->belongsToMany(ParentalEvent::class, 'parent_ev_parent', 'parrent_id', 'parental_event_id');
    }

    final public function visited_cultural_events(): BelongsToMany
    {
        return $this->belongsToMany(CulturalEvent::class, 'cult_ev_visitior', 'parrent_id', 'cultural_event_id');
    }

}
