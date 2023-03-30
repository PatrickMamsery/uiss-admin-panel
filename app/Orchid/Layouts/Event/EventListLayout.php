<?php

namespace App\Orchid\Layouts\Event;

use App\Models\Event as CustomEvent;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;

use Illuminate\Support\Str;

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
            // TD::make('id','ID')
            //     ->sort()
            //     ->filter(TD::FILTER_TEXT)
            //     ->render(function(CustomEvent $event){
            //         return Link::make($event->id)
            //         ->route('platform.event.edit',$event);
            //     }),

            TD::make('name','Name')
                ->render(function(CustomEvent $event){
                    return Link::make($event->name)
                    ->route('platform.event.edit',$event);
                }),

            TD::make('description','Description')
                ->render(function(CustomEvent $event) {
                    return Str::limit(strip_tags($event->description), 30, '...');
                }),

            TD::make('venue','Venue')
                ->render(function(CustomEvent $event){
                    return $event->venue;
                }),

            TD::make('image','Image')
                ->render(function(CustomEvent $event){
                    return '<img style=" height: 80px; width: 100px; object-fit: cover" src='.$event->image.' alt="preview"></img>';
                }),

            TD::make('Duration')
                ->render(function(CustomEvent $event){
                    return $event->start_date->toFormattedDateString().' - '.$event->end_date->toFormattedDateString();
                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (CustomEvent $event) {
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
