<?php

namespace App\Orchid\Layouts\Project;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Models\Project;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;

use Illuminate\Support\Str;

class OwnersProjectsListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'projects';

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
                ->render(function(Project $project){
                    return Link::make($project->id)
                    ->route('platform.project.edit', $project);
                }),

            TD::make('title','Title')
                ->render(function(Project $project){
                    return Link::make($project->title)
                    ->route('platform.project.edit', $project);
                }),

            TD::make('description','Description')
                ->render(function(Project $project) {
                    return Str::limit(strip_tags($project->description), 50);
                }),

            TD::make('category','Category')
                ->render(function(Project $project){
                    return $project->category->name;
                }),

            TD::make('image','Image')
                ->render(function(Project $project){
                    return '<img style=" width: 100px;" src='.$project->image.' alt="preview"></img>';
                }),
        ];
    }
}
