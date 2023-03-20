<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class University extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'name'
    ];

    public function colleges()
    {
        return $this->hasMany(College::class);
    }
}
