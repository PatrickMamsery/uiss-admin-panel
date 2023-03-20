<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class College extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'name',
        'university_id',
    ];

    public function university()
    {
        return $this->belongsTo(University::class, 'university_id', 'id');
    }
}
