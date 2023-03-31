<?php

namespace App\Orchid\Screens\Members;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Toast;
use App\Orchid\Layouts\Members\RegisteredListLayout;

use App\Models\User;
use App\Models\EventHost;
use App\Models\ProjectOwner;
use App\Models\CustomRole;
use App\Models\LeaderDetail;
use App\Models\MemberDetail;

class RegisteredListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Registered Members';

    public $description = 'List of all registered members.';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        $role = CustomRole::where('name', 'member')->first();

        return [
            'users' => User::with('memberDetails')->where('role_id', $role->id)->latest('updated_at')->paginate(),
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
            Link::make(__('Add Member'))
                ->icon('plus')
                ->route('platform.members.edit', null),
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
            RegisteredListLayout::class,
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
        if ($user->id === auth()->id()) {
            Toast::warning(__('You can not remove yourself'));

            return redirect()->route('platform.leaders');
        }

        // delete user events
        $hostedEvents = EventHost::where('user_id', $user->id)->get();
        foreach ($hostedEvents as $event) {
            $event->delete();
        }

        // delete user projects
        $hostedProjects = ProjectOwner::where('user_id', $user->id)->get();
        foreach ($hostedProjects as $project) {
            $project->delete();
        }

        // delete user membership details
        $memberDetails = MemberDetail::where('user_id', $user->id)->first();
        if ($memberDetails) $memberDetails->delete();

        // in the case where there are many entries of the same user in the member_details table
        $memberDetails = MemberDetail::where('user_id', $user->id)->get();
        foreach ($memberDetails as $memberDetail) {
            $memberDetail->delete();
        }

        // delete user leadership details in the case where there are too
        $leaderDetails = LeaderDetail::where('user_id', $user->id)->first();
        if ($leaderDetails) $leaderDetails->delete();

        // in the case where there are many entries of the same user in the leader_details table
        $leaderDetails = LeaderDetail::where('user_id', $user->id)->get();
        foreach ($leaderDetails as $leaderDetail) {
            $leaderDetail->delete();
        }

        $user->delete();

        Toast::info(__('Member was removed'));

        return redirect()->route('platform.members');
    }

    /**
     * @param User $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function promote(User $user)
    {
        $user->role_id = CustomRole::where('name', 'leader')->first()->id;

        $user->save();

        Toast::info(__('User was promoted'));

        return redirect()->route('platform.leaders');
    }
}
