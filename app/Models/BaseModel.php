<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BaseModel extends Model
{
    use HasFactory;

    protected $cacheKey = 'model';

    public function flushCache()
    {
        Cache::forget($this->getCacheKey());
    }

    protected function getCacheKey()
    {
        return $this->cacheKey . ':' . $this->id;
    }
}
