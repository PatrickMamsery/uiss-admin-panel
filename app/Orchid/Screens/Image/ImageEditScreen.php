<?php

namespace App\Orchid\Screens\Image;

use App\Models\Image;
use App\Models\Album;
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
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Cropper;
use Orchid\Support\Facades\Alert;
use Orchid\Screen\Fields\Picture;

class ImageEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Create Image';
    public $album ;

    public $description = 'Create new image';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Image $image, Album $album): array
    {
        $this->exists = $image->exists;

        if($this->exists) $this->description = 'Update Image';

        $image->load('attachment');

        return [
            'image' => $image,
            'album' => $album->id

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

                Picture::make('image.url')
                    // ->maxFileSize(5)
                    ->title('Image')
                    ->targetUrl()
                    ->required(),

                TextArea::make('image.caption')
                    ->rows(3)
                    ->title('Caption')
                    ->hidden()
                    ->value('no caption')
                    ->placeholder('Enter image caption'),

                Group::make([
                    CheckBox::make('image.visibility')
                        ->value(1)
                        ->title('Visibility')
                        ->sendTrueOrFalse()
                        ->placeholder('Visibility')
                        ->help('Will the image be visible in the website?'),

                    Select::make('image.album_id')
                        ->fromModel(Album::class, 'name')
                        ->title(__('Album name'))
                        ->help('Specify the album to which this image should belong to')
                ]),
            ])
        ];
    }

    public function createOrUpdate(Image $image, Request $request)
    {
        // echo phpinfo(); die;
        $image->fill($request->get('image'))->save();

        $image->attachment()->syncWithoutDetaching(
            $request->input('image.attachment', [])
        );

        Alert::info('Image is created successfully');

        return redirect()->route('platform.album_images', $request->image['album_id']);
    }

    public function delete(Image $image)
    {
        $id = $image->album;
        $image->delete();

        Alert::info('You have successfully deleted an image.');

        return redirect()->route('platform.album_images', $id);
    }
}
