<?php

namespace App\Orchid\Screens\Members;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Toast;
use App\Orchid\Layouts\Members\RegisteredListLayout;

use App\Models\User;
use App\Models\CustomRole;

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
            'users' => User::where('role_id', $role->id)->paginate(),
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
        $user->delete();

        Toast::info(__('User was removed'));

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
