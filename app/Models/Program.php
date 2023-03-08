<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Program extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'name',
        'description',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(ProgramCategory::class, 'category_id', 'id');
    }
}
