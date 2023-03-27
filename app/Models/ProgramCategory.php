<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class ProgramCategory extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'name',
    ];

    public function programs()
    {
        return $this->hasMany(Program::class, 'category_id');
    }
}
