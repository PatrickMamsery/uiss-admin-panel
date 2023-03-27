<?php

namespace App\Orchid\Layouts\Program;

use App\Models\Program;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

use Illuminate\Support\Str;

class ProgramListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'programs';

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
                ->render(function(Program $program){
                    return Link::make($program->id)
                    ->route('platform.program.edit',$program);
                }),

            TD::make('name','Name')
                ->render(function(Program $program){
                    return Link::make($program->name)
                    ->route('platform.program.edit',$program);
                }),

            TD::make('description','Description')
                ->render(function (Program $program) {
                    return Str::limit($program->description, 50);
                }),

            TD::make('category', 'Category')
                ->render(function(Program $program){
                    return $program->category->name;
                }),
            
            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Program $program) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.program.edit', $program)
                                ->icon('pencil'),

                            Button::make(__('Delete'))
                                ->method('remove')
                                ->confirm(__('Are you sure you want to delete the program?'))
                                ->parameters([
                                    'id' => $program->id,
                                ])
                                ->icon('trash'),
                        ]);
                }),
        ];
    }
}
