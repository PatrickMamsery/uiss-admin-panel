<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Screen\AsSource;

class Image extends Model
{
    use AsSource, Attachable;

    protected $fillable = [
        "url",
        "caption",
        "visibility",
        "album_id"
    ];

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id', 'id');
    }
}
