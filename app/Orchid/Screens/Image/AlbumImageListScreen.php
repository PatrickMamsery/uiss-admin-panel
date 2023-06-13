<?php

namespace App\Orchid\Screens\Image;

use App\Models\Image;
use App\Models\Album;
use Orchid\Screen\Screen;
use App\Orchid\Layouts\Image\AlbumImageListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Toast;

class AlbumImageListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Album | Images';
    public $album;


    /**
     * Query data.
     *
     * @return array
     */
    public function query(Album $album): array
    {
        $this->exists = $album->exists;

        if($this->exists) {
            $album = Album::find($album->id);
            $this->name = $album->name. ' | Images';
        }
        return [
            'album_image' => Image::with('album')->where('album_id', $album->id)->paginate(),
            'album' => $album->id,
        ];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make('Create new image')
                ->icon('pencil')
                ->route('platform.image.edit', $this->album)
        ];

    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            AlbumImageListLayout::class
        ];
    }

    public function remove(Request $request): void
    {
        Image::findOrFail($request->get('id'))
            ->delete();
        Toast::info(__('Image was deleted successfully'));
    }
}
