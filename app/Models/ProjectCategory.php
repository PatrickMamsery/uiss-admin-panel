<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class ProjectCategory extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'name',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
