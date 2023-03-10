<?php

namespace App\Orchid\Screens\Event;

use App\Models\Event;
use Orchid\Screen\Screen;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Cropper;
use Orchid\Support\Facades\Alert;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Layout;

class EventEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Create Event';

    public $description = 'Create event';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Event $event): array
    {
        $this->exists = $event->exists;

        if ($this->exists) {
            $this->description = 'Update event';
        }

        return [
            'event' => $event
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
                Input::make('event.name')
                    ->title('Name')
                    ->required()
                    ->placeholder('Enter event name'),

                TextArea::make('event.description')
                    ->title('Description')
                    ->rows(5)
                    ->required()
                    ->placeholder('Enter event description'),

                Input::make('event.venue')
                    ->title('Venue')
                    ->required()
                    ->placeholder('Enter event venue'),

                Input::make('event.start_date')
                    ->title('Start date')
                    ->required()
                    ->placeholder('Enter event start_date'),

                Input::make('event.end_date')
                    ->title('End date')
                    ->required()
                    ->placeholder('Enter event end_date'),

                Cropper::make('event.image')
                    ->targetId()
                    ->title('Event Image')
                    ->width(500)
                    ->height(500),
            ])
        ];
    }

    public function createOrUpdate(Event $event,Request $request )
    {


        $event->fill($request->get('event'))->save();

        Alert::info('event is created successfully');

        return redirect()->route('platform.events');
    }


    public function delete(Event $event)
    {
        $event->delete();

        Alert::info('You have successfully deleted an event.');

        return redirect()->route('platform.events');
    }
}
