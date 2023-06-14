<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Club extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'name',
        'description'
    ];

    public function leads()
    {
        return $this->hasMany(ClubLead::class);
    }

    public function members()
    {
        return $this->hasMany(ClubMember::class);
    }
}
