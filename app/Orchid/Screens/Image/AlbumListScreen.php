<?php

namespace App\Orchid\Screens\Image;

use App\Models\Image;
use App\Models\Album;
use Orchid\Screen\Screen;
use App\Orchid\Layouts\Image\AlbumListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Toast;
use Illuminate\Support\Facades\DB;

class AlbumListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Gallery | Albums';

    public $description = "Images sorted in Albums";

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'album' => Album::with('images')->paginate()
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
            Link::make('Create new album')
                ->icon('new-doc')
                ->route('platform.album.edit', null),
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
            AlbumListLayout::class
        ];
    }

    public function remove(Request $request): void
    {
        //delete album images


        try {

            DB::table('images')
                ->where('album_id', $request->get('id') )
                ->delete();
            Album::findOrFail($request->get('id'))
                ->delete();
        } catch (\Throwable $th) {
            throw $th;
        } finally{
            Toast::info(__('Album was deleted successfully'));
        }

    }
}
