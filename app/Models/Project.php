<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;
use Orchid\Attachment\Attachable;

class Project extends Model
{
    use HasFactory, AsSource, Attachable;

    protected $fillable = [
        'title',
        'description',
        'image',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id', 'id');
    }

    public function owners()
    {
        return $this->hasMany(ProjectOwner::class);
    }
}
