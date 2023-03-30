<?php

namespace App\Orchid\Screens\Members;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Toast;
use App\Orchid\Layouts\Members\LeadersListLayout;

use App\Models\User;
use App\Models\EventHost;
use App\Models\ProjectOwner;
use App\Models\LeaderDetail;
use App\Models\CustomRole;

class LeadersListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Leaders';

    public $description = 'List of all UISS leaders';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        $role = CustomRole::where('name', 'leader')->first();

        return [
            'users' => User::where('role_id', $role->id)->latest('updated_at')->paginate(),
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
            Link::make(__('Add Leader'))
                ->icon('user-follow')
                ->route('platform.leaders.edit', null),
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
            LeadersListLayout::class
        ];
    }

    /**
     * @param User $user
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(User $user)
    {
        
        // dd($user->customRole->name);
        if ($user->id === auth()->id()) {
            Toast::warning(__('You can not remove yourself'));

            return redirect()->route('platform.leaders');
        }

        $hostedEvents = EventHost::where('user_id', $user->id)->get();

        if ($hostedEvents) {
            foreach ($hostedEvents as $hostedEvent) {
                $hostedEvent->delete();
            }
        }

        // delete user projects
        $hostedProjects = ProjectOwner::where('user_id', $user->id)->get();
        foreach ($hostedProjects as $project) {
            $project->delete();
        }

        // delete user leadership details
        $leaderDetails = LeaderDetail::where('user_id', $user->id)->first();
        if ($leaderDetails) $leaderDetails->delete();

        // in the case where there are many entries of the same user in the leader_details table
        $leaderDetails = LeaderDetail::where('user_id', $user->id)->get();
        foreach ($leaderDetails as $leaderDetail) {
            $leaderDetail->delete();
        }

        $user->delete();

        Toast::info(__('User was removed'));

        return redirect()->route('platform.leaders');
    }

    /**
     * @param User $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function demote(User $user)
    {
        $user->role_id = CustomRole::where('name', 'member')->first()->id;

        $user->save();

        Toast::info(__('User was demoted'));

        return redirect()->route('platform.members');
    }
}
