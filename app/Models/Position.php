<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Position extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'title',
        'description',
    ];

    public function leaders()
    {
        return $this->hasMany(LeaderDetail::class);
    }
}
