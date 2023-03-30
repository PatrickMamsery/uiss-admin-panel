<?php

namespace App\Orchid\Screens\Event;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use App\Orchid\Layouts\Event\HostEventsListLayout;
use Orchid\Support\Facades\Toast;

use Illuminate\Http\Request;
use App\Models\Event as CustomEvent;
use App\Models\EventHost;
use App\Models\User;

class HostEventsListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Event Host | Events';

    public $description = "List of all events organized or being hosted by host";

    public $eventHost;

    /**
     * Query data.
     *
     * @return array
     */
    public function query(User $user): array
    {
        $this->exists = $user->exists;

        if ($this->exists) {
            $eventHost = EventHost::where('user_id', $user->id)->first();

            $this->name = $eventHost->user->name. ' | Events';

            $eventIds = EventHost::where('user_id', $eventHost->user->id)->get()->pluck('event_id')->toArray();
        }

        return [
            'events' => CustomEvent::whereIn('id', $eventIds)->paginate()
        ];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            HostEventsListLayout::class
        ];
    }

    public function remove(Request $request): void
    {
        $event = CustomEvent::findOrFail($request->get('id'));

        if ($event->eventHosts->count() > 0) {
            foreach ($event->eventHosts as $eventHost) {
                $eventHost->delete();
            }
            $event->delete();
        } else {
            $event->delete();
        }
        
        Toast::info(__('Event was deleted'));
    }
}
