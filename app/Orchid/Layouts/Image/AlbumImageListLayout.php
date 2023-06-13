<?php

namespace App\Orchid\Layouts\Image;

use App\Models\Album;
use App\Models\Image;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class AlbumImageListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'album_image';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('preview', 'Preview')
                ->render(function(Image $image){
                    return '<img style=" width: 100px;" src='.$image->url.' alt="preview"></img>';
                }),

            TD::make('created_at', 'Created')
                ->render(function(Image $image) {
                    return $image->created_at->toDateTimeString();
                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Image $image) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([

                            Link::make(__('Edit'))
                                ->route('platform.image.edit', $image->id)
                                ->icon('pencil'),

                            Button::make(__('Delete'))
                                ->icon('trash')
                                ->confirm(__('Service will be removed permanently. Please confirm submission.'))
                                ->method('remove', [
                                    'id' => $image->id,
                                ]),
                        ]);
                }),
        ];
    }
}
