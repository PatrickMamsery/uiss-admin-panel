<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Album;
use App\Models\Image;
use App\Http\Resources\ImageResource;
use App\Http\Resources\AlbumResource;
use App\Http\Resources\AlbumImagesResource;

class GalleryController extends BaseController
{
    // IMAGE MANIPULATION

    /**
     * Get all images in the gallery
     *
     * @return \Illuminate\Http\Response
     */
    public function getImages()
    {
        $per_page = 15;
        return $this->sendResponse(ImageResource::collection(Image::with('album')->latest('updated_at')->paginate($per_page)), 'RETRIEVE_SUCCESS');
    }

    /**
     * Get a single image
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function getImage($id) {
        $image = Image::find($id);

        if (is_null($image)) {
            return $this->sendError('RETRIEVE_FAILED');
        }
        return $this->sendResponse(new ImageResource($image), 'RETRIEVE_SUCCESS');
    }

    /**
     * Create an image
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'album' => 'required',
            'caption' => 'nullable',
            'visibility' => 'nullable',
            'url' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        try {
            // check if album exists
            $album = Album::where('name', $request->album)->first();

            if (is_null($album)) {
                return $this->sendError('NOT_FOUND', 'Album not found');
            } else {
                $image = Image::create([
                    'album_id' => $album->id,
                    'caption' => $request->caption,
                    'visibility' => $request->visibility,
                    'url' => $request->url,
                ]);

                return $this->sendResponse(new ImageResource($image), 'CREATE_SUCCESS');
            }
        } catch (\Throwable $th) {
            return $this->sendError('CREATE_FAILED', $th->getMessage());
        }
    }

    /**
     * Update an image
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function updateImage(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'album' => 'required',
            'caption' => 'nullable',
            'visibility' => 'nullable',
            'url' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        try {
            $image = Image::find($id);

            if (is_null($image)) {
                return $this->sendError('NOT_FOUND');
            } else {
                // flow
                // 1. check if album_id is different from current album_id
                // 2. if different, check if new album_id exists
                // 3. if new album_id exists, update image
                // 4. if new album_id does not exist, return error
                // 5. if album_id is the same, update image

                if ($image->album_id != Album::where('name', $request->album)->first()->id) {
                    $album = Album::where('name', $request->album)->first();

                    if (is_null($album)) {
                        return $this->sendError('UPDATE_FAILED', 'Album does not exist');
                    }
                } else {
                    $album = Album::find($image->album_id);
                    $image->album()->associate($album);
                    $image->update($request->all());
                }

                return $this->sendResponse(new ImageResource($image), 'UPDATE_SUCCESS');
            }

        } catch (\Throwable $th) {
            return $this->sendError('UPDATE_FAILED', $th->getMessage());
        }
    }

    /**
     * Delete an image
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteImage($id)
    {
        try {
            $image = Image::find($id);

            if (is_null($image)) {
                return $this->sendError('NOT_FOUND');
            } else {
                $image->delete();
                return $this->sendResponse(new ImageResource($image), 'DELETE_SUCCESS', 204);
            }
        } catch (\Throwable $th) {
            return $this->sendError('DELETE_FAILED', $th->getMessage());
        }
    }


    // ALBUM MANIPULATION

    // get all albums
    public function getAlbums()
    {
        $per_page = 15;
        return $this->sendResponse(AlbumResource::collection(Album::latest('updated_at')->paginate($per_page)), 'RETRIEVE_SUCCESS');
    }

    // get single album
    /**
     * Get a single album
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getAlbum($id)
    {
        $album = Album::find($id);

        if (is_null($album)) {
            return $this->sendError('RETRIEVE_FAILED');
        } else {
            $album->load('images');
            return $this->sendResponse(new AlbumResource($album), 'RETRIEVE_SUCCESS');
        }
    }

    // get all images in an album
    /**
     * Get all images in an album
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getAlbumImages($id)
    {
        $album = Album::find($id);

        if (is_null($album)) {
            return $this->sendError('RETRIEVE_FAILED');
        } else {
            $album->load('images');
            return $this->sendResponse(AlbumImagesResource::collection($album->images), 'RETRIEVE_SUCCESS');
        }
    }

    // create an album
    /**
     * Create an album
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function createAlbum(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:albums',
            'visibility' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        try {
            $album = Album::create([
                'name' => $request->name,
                'visibility' => $request->visibility ?? 1,
            ]);

            return $this->sendResponse(new AlbumResource($album), 'CREATE_SUCCESS');
        } catch (\Throwable $th) {
            return $this->sendError('CREATE_FAILED', $th->getMessage());
        }
    }

    // update an album
    /**
     * Update an album
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateAlbum(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:albums',
            'visibility' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        try {
            $album = Album::find($id);

            if (is_null($album)) {
                return $this->sendError('NOT_FOUND');
            } else {
                $album->update($request->all());
                return $this->sendResponse(new AlbumResource($album), 'UPDATE_SUCCESS');
            }
        } catch (\Throwable $th) {
            return $this->sendError('UPDATE_FAILED', $th->getMessage());
        }
    }

    // delete an album
    /**
     * Delete an album
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteAlbum($id)
    {
        try {
            $album = Album::find($id);

            if (is_null($album)) {
                return $this->sendError('NOT_FOUND');
            } else {
                // remember to delete all images in the album
                foreach ($album->images as $image) {
                    $image->delete();
                }
                
                $album->delete();
                return $this->sendResponse(new AlbumResource($album), 'DELETE_SUCCESS', 204);
            }
        } catch (\Throwable $th) {
            return $this->sendError('DELETE_FAILED', $th->getMessage());
        }
    }
}
