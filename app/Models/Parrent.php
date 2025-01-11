<?php

namespace App\Models;

use App\Interfaces\ModelInterfaces\SearchableInterface;
use App\Traits\HasUser;
use App\Traits\Sortable;
use Illuminate\Support\Collection;
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

    public function scopeMaritalStatuses($query): Collection
    {
        return $query->distinct()->pluck('marital_status');
    }

    final public function children_relations(): BelongsToMany
    {
        return $this->belongsToMany(Children::class, 'child_parent_relations', 'parrent_id', 'child_id')->withPivot('relations');
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
