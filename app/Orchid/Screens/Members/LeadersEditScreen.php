<?php

namespace App\Orchid\Screens\Members;

use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Group;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Alert;

use Orchid\Screen\Actions\ModalToggle;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\EventHost;
use App\Models\ProjectOwner;
use App\Models\Position;
use App\Models\LeaderDetail;
use App\Models\CustomRole;

class LeadersEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Leader';

    public $description = 'Create a new leader.';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(User $user): array
    {
        $this->exists = $user->exists;

        if ($this->exists) {
            $this->name = 'Edit Leader';
            $this->description = 'Edit leader\'s details.';
        }

        return [
            'user' => $user,
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
            ModalToggle::make('Create New Position')
                ->icon('plus')
                ->modal('createNewPositionModal')
                ->method('createPosition'),
                // ->canSee(!$this->exists),

            Button::make('Create')
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee(!$this->exists),

            Button::make('Update')
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee($this->exists),

            Button::make('Delete')
                ->icon('trash')
                ->method('delete')
                ->canSee($this->exists)
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
            Layout::modal('createNewPositionModal', [
                Layout::rows([
                    Input::make('position.title')
                        ->title('Position')
                        ->required()
                        ->placeholder('Chairman')
                        ->help('Enter the position of the leader'),
                ])
            ])->title('Create New Position')->applyButton('Create'),

            Layout::rows([
                Group::make([
                    Input::make('user.name')
                        ->title('Name')
                        ->required()
                        ->placeholder('John Doe')
                        ->help('Enter the name of the leader'),

                    Input::make('user.email')
                        ->title('Email')
                        ->required()
                        ->placeholder('leader@example.com')
                        ->help('Enter the email of the leader'),

                    Input::make('user.phone')
                        ->title('Phone')
                        ->required()
                        ->placeholder('(255)678909876')
                        ->help('Enter the phonenumber of the leader'),

                    Select::make('user.position')
                        ->title('Position')
                        ->fromModel(Position::class, 'title')
                        ->required()
                        ->help('Enter leader\'s position'),
                ])
            ])
        ];
    }

    /**
     * @param User    $user
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(User $user, Request $request)
    {
        $user->password = bcrypt($request->get('user')['phone']);
        $user->role_id = CustomRole::where('name', 'leader')->first()->id;

        $user->fill($request->get('user'))->save();

        Alert::info('You have successfully created a leader.');

        return redirect()->route('platform.leaders');
    }

    /**
     * @param User $user
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(User $user)
    {
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

        $user->delete()
            ? Alert::info('You have successfully deleted the leader.')
            : Alert::error('An error has occurred');

        return redirect()->route('platform.leaders');
    }

    public function createPosition(Request $request)
    {
        $position = Position::where('title', $request->get('position')['title'])->first();

        if ($position) {
            Alert::error('Position already exists.');
            return redirect()->route('platform.leaders');
        } else {
            $position = new Position;
            $position->title = $request->get('position')['title'];
            $position->save();

            Alert::info('You have successfully created a position.');

            return redirect()->route('platform.leaders');
        }
    }
}
