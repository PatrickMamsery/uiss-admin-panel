<?php

namespace App\Orchid\Screens\Image;

use App\Models\Image;
use Orchid\Screen\Screen;
use App\Orchid\Layouts\Image\ImageListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Toast;

class ImageListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Image Gallery';

    public $description = "Images to be viewed in gallery section";

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'image' => Image::with('album')->paginate()
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
                ->route('platform.image.edit',  ),
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
            ImageListLayout::class
        ];
    }

    public function remove(Request $request): void
    {
        Image::findOrFail($request->get('id'))
            ->delete();

        Toast::info(__('Image was deleted successfully'));
    }
}
