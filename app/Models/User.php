<?php

namespace App\Models;

use Orchid\Platform\Models\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'role_id',
        'image',
        'isProjectOwner',
        'password',
        'permissions',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permissions'          => 'array',
        'email_verified_at'    => 'datetime',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'id',
        'name',
        'phone',
        'isProjectOwner',
        'email',
        'permissions',
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'name',
        'phone',
        'email',
        'updated_at',
        'created_at',
    ];

    public function customRole()
    {
        return $this->belongsTo(CustomRole::class, 'role_id', 'id');
    }

    public function owns()
    {
        return $this->hasMany(ProjectOwner::class);
    }

    public function hosts()
    {
        return $this->hasMany(EventHost::class);
    }

    public function memberDetails()
    {
        return $this->hasOne(MemberDetail::class);
    }

    public function leaderDetails()
    {
        return $this->hasOne(LeaderDetail::class);
    }
}
