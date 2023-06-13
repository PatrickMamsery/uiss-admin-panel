<?php

namespace App\Orchid\Layouts\Image;

use App\Models\Album;
use App\Models\Image;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class AlbumListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'album';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('name', 'Name')
                ->render(function(Album $album) {
                    return Link::make($album->name)
                    ->route('platform.album_images', $album);
                }),

            TD::make('images', '# of Images')
                ->render(function(Album $album) {
                    return $album->images->count();
                }),

            TD::make('created_at', 'Created')
                ->render(function(Album $album) {
                    return $album->created_at->toDateTimeString();
                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Album $album) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([

                            Link::make(__('Edit'))
                                ->route('platform.album.edit', $album->id)
                                ->icon('pencil'),

                            Button::make(__('Delete'))
                                ->icon('trash')
                                ->confirm(__('Album and all its images will be removed permanently. Please confirm submission.'))
                                ->method('remove', [
                                    'id' => $album->id,
                                ]),
                        ]);
                }),
        ];
    }
}
