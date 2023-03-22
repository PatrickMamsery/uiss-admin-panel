<?php

namespace App\Traits;

use App\Mail\PasswordReset;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

trait CloudinaryManagementTrait{

    public function uploadImage($image)
    {
        // check if image is null
        if (!$image) {
            return null;
        }
        
        $path = Cloudinary::upload($image->getRealPath(), [
            'folder' => 'laravel-endpoint'
        ])->getSecurePath();

        return $path;
    }

    public function deleteImage($image)
    {
        $public_id = preg_match("/upload\/(?:v\d+\/)?([^\.]+)/", $image, $matches);

        // for development purposes... should change to return false in production
        if (!$public_id || !isset($matches[1])) {
            return true;
        }

        Cloudinary::destroy($matches[1]);

        return true;
    }

}