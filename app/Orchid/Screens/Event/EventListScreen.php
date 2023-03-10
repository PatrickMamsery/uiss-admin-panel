<?php

namespace App\Orchid\Screens\Event;

use Illuminate\Http\Request;
use App\Models\Event as CustomEvent;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use App\Orchid\Layouts\Event\EventListLayout;
use Orchid\Support\Facades\Toast;

class EventListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Events';
    public $description = "All Events";

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'events' => CustomEvent::paginate()
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
            Link::make('Create new')
                ->icon('pencil')
                ->route('platform.event.edit',null)
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
            EventListLayout::class
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
