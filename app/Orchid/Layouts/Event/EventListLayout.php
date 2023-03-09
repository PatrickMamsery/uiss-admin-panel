<?php

namespace App\Orchid\Layouts\Event;

use App\Models\Event;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;

class EventListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'events';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('id','ID')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function(Event $event){
                    return Link::make($event->id)
                    ->route('platform.event.edit',$event);
                }),

            TD::make('name','Name')
                ->render(function(Event $event){
                    return Link::make($event->name)
                    ->route('platform.event.edit',$event);
                }),

            TD::make('description','Description'),

            TD::make('venue','Venue')
                ->render(function(Event $event){
                    return Link::make($event->venue)
                    ->route('platform.event.edit', $event->venue);
                }),

            TD::make('image','Image')
                ->render(function(Event $event){
                    return Link::make($event->image)
                    ->route('platform.event.edit', $event->image);
                }),

            TD::make('start_date','start_date')
                ->render(function(Event $event){
                    return Link::make($event->start_date)
                    ->route('platform.event.edit', $event->start_date);
                }),

            TD::make('end_date','end_date')
                ->render(function(Event $event){
                    return Link::make($event->end_date)
                    ->route('platform.event.edit', $event->end_date);
                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Event $event) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.event.edit', $event)
                                ->icon('pencil'),

                            Button::make(__('Delete'))
                                ->method('remove')
                                ->confirm(__('Are you sure you want to delete the event?'))
                                ->parameters([
                                    'id' => $event->id,
                                ])
                                ->icon('trash'),
                        ]);
                }),
        ];
    }
}
