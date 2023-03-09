<?php

namespace App\Orchid\Screens\Project;

use App\Models\Project;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use App\Orchid\Layouts\Project\ProjectListLayout;

class ProjectListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Projects';
    public $description = "All Projects";

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'projects' => Project::paginate()
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
                ->route('platform.project.edit',null)
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
           ProjectListLayout::class
        ];
    }
}
