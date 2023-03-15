<?php

namespace App\Orchid\Screens\Members;

use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Group;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Alert;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\CustomRole;

class RegisteredEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Member';

    public $description = 'Create a new member.';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(User $user): array
    {
        $this->exists = $user->exists;

        if ($this->exists) {
            $this->name = 'Edit Member';
            $this->description = 'Edit member details.';
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
            Layout::rows([
                Group::make([
                    Input::make('user.name')
                        ->title('Name')
                        ->required()
                        ->placeholder('John Doe')
                        ->help('Enter the name of the member'),

                    Input::make('user.email')
                        ->title('Email')
                        ->required()
                        ->placeholder('member@example.com')
                        ->help('Enter the email of the member'),

                    Input::make('user.phone')
                        ->title('Phone')
                        ->required()
                        ->placeholder('(255)678909876')
                        ->help('Enter the phonenumber of the member'),
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
        $user->role_id = CustomRole::where('name', 'member')->first()->id;

        $user->fill($request->get('user'))->save();

        Alert::info('You have successfully created a member.');

        return redirect()->route('platform.members');
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
        $user->delete()
            ? Alert::info('You have successfully deleted the member.')
            : Alert::error('An error has occurred');

        return redirect()->route('platform.members');
    }
}
