<?php

namespace App\Orchid\Screens\Project;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use App\Orchid\Layouts\Project\OwnersProjectsListLayout;
use Orchid\Support\Facades\Toast;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectOwner;
use App\Models\User;

class OwnersProjectsListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Project Owner| Projects';

    public $description = 'List of all projects owned by a project owner';

    public $projectOwner;

    /**
     * Query data.
     *
     * @return array
     */
    public function query(User $user): array
    {
        $this->exists = $user->exists;

        if ($this->exists) {
            $projectOwner = ProjectOwner::where('user_id', $user->id)->first();

            $this->name = $projectOwner->user->name. ' | Projects';

            $projectIds = ProjectOwner::where('user_id', $projectOwner->user->id)->get()->pluck('project_id')->toArray();
            // $projectIds = ProjectOwner::where('user_id', $projectOwner->user->id)->first();
        }

        // dd(Project::whereIn('id', $projectIds)->get());

        return [
            'projects' => Project::whereIn('id', $projectIds)->paginate()
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
            OwnersProjectsListLayout::class
        ];
    }
}
