<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class ClubLead extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'club_id',
        'user_id'
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function leadDetails()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
