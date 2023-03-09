<?php

namespace App\Orchid\Screens\Event;

use App\Models\Event;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use App\Orchid\Layouts\Event\EventListLayout;

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
            'events' => Event::paginate()
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
}
