<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Event extends BaseModel
{
    use HasFactory, AsSource;

    protected $fillable = [
        'name',
        'description',
        'venue',
        'image',
        'start_date',
        'end_date',
    ];

    protected $dates = [
        'start_date',
        'end_date',
    ];

    public function eventHosts()
    {
        return $this->hasMany(EventHost::class);
    }

    public function attendees()
    {
        return $this->hasMany(EventRegistration::class);
    }
}
