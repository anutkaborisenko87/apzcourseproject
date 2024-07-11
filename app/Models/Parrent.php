<?php

namespace App\Models;

use App\Interfaces\ModelInterfaces\SearchableInterface;
use App\Traits\HasUser;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Parrent extends Model implements SearchableInterface
{
    use HasFactory;
    use HasUser;
    use Sortable;

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
        return $this->belongsToMany(ParentalEvent::class, 'parent_ev_parent', 'parrent_id', 'parental_event_id')->withPivot('result');
    }

    final public function visited_cultural_events(): BelongsToMany
    {
        return $this->belongsToMany(CulturalEvent::class, 'cult_ev_visitior', 'parrent_id', 'cultural_event_id')->withPivot('reaction');
    }

    public static function getSearchableFields(): array
    {
        return [
            'phone',
            'work_place',
            'passport_data',
            'marital_status'
        ];
    }
}
