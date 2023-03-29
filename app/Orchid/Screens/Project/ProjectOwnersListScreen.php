<?php

namespace App\Orchid\Screens\Project;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use App\Orchid\Layouts\Project\ProjectOwnersListLayout;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectOwner;

class ProjectOwnersListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Project Owners';

    public $description = 'List of all people involved in or owning projects';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        $projectOwnersIds = ProjectOwner::with('user')
            ->get()
            ->pluck('user_id')
            ->toArray();

        // dd($projectOwnersIds);

        return [
            'owners' => User::with('owns')
                ->where('isProjectOwner', 1)
                ->whereIn('id', $projectOwnersIds)
                ->latest('updated_at')
                ->paginate(),
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
            Layout::modal('editProjectOwnerDetails', [
                Layout::rows([
                    Input::make('user.name')
                        ->title('Name')
                        ->required(),
                ]),
            ])
                ->async('asyncEditProjectOwner')
                ->title('Edit Project Owner\'s Details'),

            ProjectOwnersListLayout::class,
        ];
    }

    public function asyncEditProjectOwner(User $user): array
    {
        return [
            'user' => $user,
        ];
    }

    public function editProjectOwner(User $user, Request $request)
    {
        $request->validate([
            'user.name' => 'required',
        ]);

        $user->fill($request->input('user'))->save();

        Toast::info('Owner\'s name edited successfully');

        return redirect()->route('platform.project-owners');
    }
}
