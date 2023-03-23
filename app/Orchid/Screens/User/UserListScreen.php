<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Orchid\Layouts\User\UserEditLayout;
use App\Orchid\Layouts\User\UserFiltersLayout;
use App\Orchid\Layouts\User\UserListLayout;
use Illuminate\Http\Request;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

use App\Models\LeaderDetail;
use App\Models\MemberDetail;

class UserListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'User';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'All registered users';

    /**
     * @var string
     */
    public $permission = 'platform.systems.users';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'users' => User::with('roles')
                ->filters()
                ->filtersApplySelection(UserFiltersLayout::class)
                ->defaultSort('id', 'desc')
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
        return [
            Link::make(__('Add'))
                ->icon('plus')
                ->route('platform.systems.users.create'),
        ];
    }

    /**
     * Views.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): array
    {
        return [
            UserFiltersLayout::class,
            UserListLayout::class,

            Layout::modal('oneAsyncModal', UserEditLayout::class)
                ->async('asyncGetUser'),
        ];
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function asyncGetUser(User $user): array
    {
        return [
            'user' => $user,
        ];
    }

    /**
     * @param User    $user
     * @param Request $request
     */
    public function saveUser(User $user, Request $request): void
    {
        $request->validate([
            'user.email' => 'required|unique:users,email,'.$user->id,
        ]);

        $user->fill($request->input('user'))
            ->save();

        Toast::info(__('User was saved.'));
    }

    /**
     * @param Request $request
     */
    public function remove(Request $request): void
    {
        $user = User::with('customRole', 'hosts', 'owns')->findOrFail($request->get('id'));
        
        if (!$user) Toast::error(__('User was not found'));

        if ($user->customRole->name === 'admin') {
            Toast::warning(__('You can not remove admin'));
        }

        try {
            // check if user has relations then delete them
            if ($user->customRole->name == 'member') {
                $memberDetails = MemberDetail::where('user_id', $user->id)->get();
                if (count($memberDetails) > 0) {
                    foreach ($memberDetails as $memberDetail) {
                        $memberDetail->delete();
                    }
                } else {
                    $member = MemberDetail::where('user_id', $user->id)->first();
                    if ($member) $member->delete();
                }
            } else if ($user->customRole->name == 'leader') {
                $leaderDetails = LeaderDetail::where('user_id', $user->id)->get();
                if (count($leaderDetails) > 0) {
                    foreach ($leaderDetails as $leaderDetail) {
                        $leaderDetail->delete();
                    }
                } else {
                    $leader = LeaderDetail::where('user_id', $user->id)->first();
                    if ($leader) $leader->delete();
                }
            }

            if (count($user->hosts) > 0) {
                foreach ($user->hosts as $host) {
                    $host->delete();
                }
            } else if (count($user->owns) > 0) {
                foreach ($user->owns as $own) {
                    $own->delete();
                }
            }

        } catch (\Throwable $th) {
            Toast::error(__('User was not removed'.$th->getMessage()));
        }

        Toast::info(__('User was removed'));
    }
}
