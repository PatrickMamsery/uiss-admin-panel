<?php

namespace App\Orchid\Screens\Event;

use App\Models\Event as CustomEvent;
use App\Models\EventHost;
use Orchid\Screen\Screen;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Cropper;
use Orchid\Support\Facades\Alert;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\ModalToggle;

use App\Models\User;
use App\Traits\CloudinaryManagementTrait as CloudinaryManagement;

class EventEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Create Event';

    public $description = 'Create event';

    public $event;

    /**
     * Query data.
     *
     * @return array
     */
    public function query(CustomEvent $event): array
    {
        $this->exists = $event->exists;

        if ($this->exists) {
            $this->event = $event;
            $this->name = 'Update "'. $event->name . '" Details';
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
            ModalToggle::make('Add Event Host')
                ->modal('createHostModal')
                ->method('createHost')
                ->icon('plus'),

            Button::make('Create')
                ->icon('note')
                ->method('createEvent')
                ->canSee(!$this->exists),

            Button::make('Update')
                ->icon('pencil')
                ->method('updateEvent')
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
            Layout::modal('createHostModal', [
                Layout::rows([
                    Input::make('hosts')
                        ->title('Event Host(s)')
                        // ->multiple()
                        ->placeholder('Enter event host\'s full name')
                        ->help('Enter event host\'s full name if not in the available database entries. For multiple hosts, separate each name with a comma and a single whitespace.'),
                ])
            ])->title('Add Event Host'),

            Layout::rows([
                Group::make([
                    Input::make('event.name')
                        ->title('Name')
                        ->required()
                        ->placeholder('Enter event name'),

                    Input::make('event.venue')
                        ->title('Venue')
                        ->required()
                        ->placeholder('Enter event venue'),
                ]),

                Group::make([
                    Select::make('event.hosts.')
                        ->title('Event Hosts')
                        ->fromModel(User::class, 'name')
                        ->multiple()
                        ->required()
                        ->help('Choose from available entries in the database. If the required name is not in the list create a new entry using the "Add Event Host" button above. You may also add multiple hosts by holding down the "Ctrl" key and selecting multiple entries.'),
                        // ->canSee(!$this->exists),

                    // a readonly input attribute that will have values of already assigned hosts from the database
                    Input::make('event_hosts')
                        ->title('Event Host(s) already assigned to the event')
                        ->readonly()
                        ->value(implode(', ', EventHost::with('user')->where('event_id', ($this->exists ? $this->event->id : "none"))->get()->pluck('user.name')->toArray()))
                        ->help('Already assigned hosts')
                        ->canSee($this->exists),

                ]),

                Quill::make('event.description')
                    ->title('Description')
                    ->required()
                    ->placeholder('Enter event description'),

                Group::make([
                    DateTimer::make('event.start_date')
                        ->title('Start date')
                        ->required(),

                    DateTimer::make('event.end_date')
                        ->title('End date')
                        ->required(),
                ]),

                Cropper::make('event.image')
                    ->targetUrl()
                    // ->required()
                    ->title('Event Image'),
            ])
        ];
    }

    public function createEvent(CustomEvent $event, Request $request)
    {
        $event->fill($request->get('event'))->save();

        // create new hosts
        foreach ($request->get('event')['hosts'] as $host) {
            // save entries in event_hosts table
            EventHost::firstOrCreate([
                'user_id' => $host,
                'event_id' => $event->id
            ]);
        }

        Alert::info('Event is created successfully');

        return redirect()->route('platform.events');
    }

    public function updateEvent(CustomEvent $event, Request $request)
    {
        $event->fill($request->get('event'))->save();

        // delete current hosts
        EventHost::where('event_id', $event->id)->delete();

        // create new hosts
        foreach ($request->get('event')['hosts'] as $host) {
            // save entries in event_hosts table
            EventHost::firstOrCreate([
                'user_id' => $host,
                'event_id' => $event->id
            ]);
        }

        Alert::info('Event is updated successfully');

        return redirect()->route('platform.events');
    }

    public function createHost(Request $request)
    {
        $hostsEntries = explode(', ', $request->get('hosts'));

        foreach ($hostsEntries as $host) {
            // check if the hosts are in the database
            $user = User::where('name', $host)->first();

            if ($user) {
                continue; // skip if the host is already in the database
            } else {
                // create new user
                $user = new User;
                $user->name = $host;
                $user->email = strtolower(preg_replace('/\s+/', '', $host)) . '@example.com';
                $user->role_id = 5; // member
                $user->password = bcrypt('password');
                $user->save();

                $hostId = $user->id;
            }
        }
    }


    public function delete(CustomEvent $event)
    {
        $event->delete();

        Alert::info('You have successfully deleted an event.');

        return redirect()->route('platform.events');
    }
}
