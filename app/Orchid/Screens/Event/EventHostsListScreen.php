<?php

namespace App\Orchid\Screens\Event;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use App\Orchid\Layouts\Event\EventHostsListLayout;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Event;
use App\Models\EventHost;

class EventHostsListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Event Hosts | Organizers';

    public $description = 'List of all people organizing or hosting events';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        $eventHostsIds = EventHost::with('user')
            ->get()
            ->pluck('user_id')
            ->toArray();


        return [
            'hosts' => User::with('hosts')
                // ->where('isEventHost', 1)
                ->whereIn('id', $eventHostsIds)
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
            Layout::modal('editEventHostDetails', [
                Layout::rows([
                    Input::make('user.name')
                        ->title('Name')
                        ->required(),
                ])
            ])->async('asyncEditEventHost')
                ->title('Edit Event Host\'s Details'),

            EventHostsListLayout::class
        ];
    }

    public function asyncEditEventHost(User $user): array
    {
        return [
            'user' => $user,
        ];
    }

    public function editEventHost(User $user, Request $request)
    {
        $request->validate([
            'user.name' => 'required',
        ]);

        $user->fill($request->input('user'))->save();

        Toast::info('Host\'s name edited successfully');

        return redirect()->route('platform.event-hosts');
    }
}
