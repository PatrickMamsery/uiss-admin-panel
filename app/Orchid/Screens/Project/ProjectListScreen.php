<?php

namespace App\Orchid\Screens\Project;

use Illuminate\Http\Request;
use App\Models\Project;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use App\Orchid\Layouts\Project\ProjectListLayout;
use Orchid\Support\Facades\Toast;

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

    public function remove(Request $request): void
    {
        $project = Project::findOrFail($request->get('id'));

        if ($project->owners->count() > 0) {
            foreach ($project->owners as $owner) {
                $owner->delete();
            }

            $project->delete();
        } else {
            $project->delete();
        }

        Toast::info(__('Project was deleted'));
    }
}
