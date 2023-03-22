<?php

namespace App\Orchid\Screens\Members;

use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Group;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Alert;

use Illuminate\Http\Request;

use App\Orchid\Layouts\ExtraDetailsListener;

use App\Models\User;
use App\Models\CustomRole;
use App\Models\University;
use App\Models\College;
use App\Models\Department;
use App\Models\DegreeProgramme;
use App\Models\MemberDetail;

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
     * @param User $user
     * @param Request $request
     */
    public function asyncGetExtraDetails(int $university_id = null, int $college_id = null, int $department_id = null)
    {
        dd($university_id, $college_id, $department_id);
        $universities = University::all();
        $colleges = College::where('university_id', $university_id)->get();
        $departments = Department::where('college_id', $college_id)->get();
        $degreeProgrammes = DegreeProgramme::where('department_id', $department_id)->get();

        return [
            'universities' => $universities,
            'colleges' => $colleges,
            'departments' => $departments,
            'degreeProgrammes' => $degreeProgrammes,
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
            // Layout::rows([
                

            //     // Group::make([
            //     //     Select::make('member.university_id')
            //     //         ->title('University')
            //     //         ->fromModel(University::class, 'name')
            //     //         ->onselect('universitySelected')
            //     //         ->help('Select university that the member is from'),
                    
            //     //     Select('member.college_id')
            //     //         ->title('College')
            //     //         ->fromQuery(College::where('university_id', $this->member->university_id)->get(), 'name')
            //     //         ->help('Select college that the member is from'),

            //     //     Select('member.department_id')
            //     //         ->title('Department')
            //     //         ->fromQuery(Department::where('college_id', $this->member->college_id)->get(), 'name')
            //     //         ->help('Select department that the member is from'),

            //     //     Select('member.degree_programme_id')
            //     //         ->title('Degree Programme')
            //     //         ->fromQuery(DegreeProgramme::where('department_id', $this->member->department_id)->get(), 'name')
            //     //         ->help('Select degree programme that the member is from'),
            //     // ])
            // ])
            ExtraDetailsListener::class,
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

        $memberDetail = MemberDetail::where('user_id', $user->id)->first();

        if ($memberDetail) {
            $memberDetail->fill($request->get('member'))->save();
        } else {
            $memberDetail = new MemberDetail($request->get('member'));
            $memberDetail->user_id = $user->id;
            $memberDetail->save();
        }

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

    // public function universitySelected(Request $request)
    // {
    //     $university = University::find($request->get('university_id'));

    //     dd($university);

    //     return [
    //         'member.college_id' => $university->colleges()->get(['id', 'name']),
    //         'member.department_id' => $university->departments()->get(['id', 'name']),
    //         'member.degree_programme_id' => $university->degreeProgrammes()->get(['id', 'name']),
    //     ];
    // }
}
