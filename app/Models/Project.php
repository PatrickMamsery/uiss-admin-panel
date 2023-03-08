<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Project extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'title',
        'description',
        'image',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(ProjectCategory::class);
    }
}
