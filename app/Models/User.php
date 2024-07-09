<?php

namespace App\Models;

use App\Interfaces\ModelInterfaces\SearchableInterface;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements SearchableInterface
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    use Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'last_name',
        'first_name',
        'patronymic_name',
        'sex',
        'email',
        'password',
        'city',
        'street',
        'house_number',
        'apartment_number',
        'active',
        'birth_date',
        'birth_year',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    final public function userCategory(): string
    {
        if (!is_null($this->employee)) {
            return 'employee';
        }

        if (!is_null($this->parrent)) {
            return 'parent';
        }

        if (!is_null($this->children)) {
            return 'children';
        }

        return 'admin';
    }

    final public function parrent(): HasOne
    {
        return $this->hasOne(Parrent::class, 'user_id', 'id');
    }

    final public function children(): HasOne
    {
        return $this->hasOne(Children::class, 'user_id', 'id');
    }

    final public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'user_id', 'id');
    }

    public static function getSearchableFields(): array
    {
        return [
            'last_name',
            'first_name',
            'patronymic_name',
            'email',
            'city',
            'street',
            'house_number',
            'apartment_number',
            'birth_date',
            'birth_year',
        ];
    }
}
