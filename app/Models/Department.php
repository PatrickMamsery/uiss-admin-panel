<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Department extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'name',
        'college_id'
    ];

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id', 'id');
    }
}
