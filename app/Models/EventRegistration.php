<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class EventRegistration extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'event_id',
        'user_id',
        'status',
    ];

    public function event()
    {
        return $this->belongsTo(\App\Models\Event::class, 'event_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'guest',
        ]);
    }
}
