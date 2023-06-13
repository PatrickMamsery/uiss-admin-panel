<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Album extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        "name",
        "visibility"
    ];

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
