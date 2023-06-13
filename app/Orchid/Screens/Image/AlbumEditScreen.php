<?php

namespace App\Orchid\Screens\Image;

use App\Models\Album;
use App\Models\Image;
use Orchid\Screen\Screen;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Relation;
use Orchid\Support\Facades\Alert;

class AlbumEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Create Album';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Album $album): array
    {
        $this->exists = $album->exists;

        if($this->exists) $this->description = 'Update Album';

        return [
            'album' => $album
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
            Button::make('Create')
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee(!$this->exists),

            Button::make('Update')
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee($this->exists),

            Button::make('Delete')
                ->icon('trash')
                ->method('delete')
                ->canSee($this->exists)
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
            Layout::rows([
                Input::make('album.name')
                    ->title('Album name')
                    ->required(),

                CheckBox::make('album.visibility')
                    ->value(1)
                    ->title('Visibility')
                    ->sendTrueOrFalse()
                    ->placeholder('Visibility')
                    ->help('Will the album be visible in the website?'),
            ])
        ];
    }

    public function createOrUpdate(Album $album,Request $request )
    {
        $album->fill($request->get('album'))->save();

        // $image->attachment()->syncWithoutDetaching(
        //     $request->input('service.attachment', [])
        // );

        Alert::info('Album is created successfully');

        return redirect()->route('platform.albums');
    }

    public function delete(Album $album)
    {
        $album->delete();

        Alert::info('You have successfully deleted an album.');

        return redirect()->route('platform.albums');
    }
}
