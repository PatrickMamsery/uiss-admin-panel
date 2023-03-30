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

use App\Orchid\Layouts\ExtraDetailsListener;

use App\Models\User;
use App\Models\EventHost;
use App\Models\ProjectOwner;
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
            'user' => $user->load('memberDetails'),
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
            ModalToggle::make('Create New Detail')
                ->modal('createNewDetailModal')
                ->method('createDetail')
                ->icon('plus')
                ->canSee(!$this->exists),

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
        return [
            'universities' => University::all(),
            'colleges' => College::where('university_id', $university_id)->get(),
            'departments' => Department::where('college_id', $college_id)->get(),
            'degreeProgrammes' => DegreeProgramme::where('department_id', $department_id)->get(),
        ];

        // $universities = University::all();
        // $colleges = College::where('university_id', $this->university_id)->get();
        // $departments = Department::where('college_id', $this->college_id)->get();
        // $degreeProgrammes = DegreeProgramme::where('department_id', $this->department_id)->get();

        // return [
        //     'universities' => $universities,
        //     'colleges' => $colleges,
        //     'departments' => $departments,
        //     'degreeProgrammes' => $degreeProgrammes,
        // ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            Layout::modal('createNewDetailModal', [
                Layout::rows([
                    Input::make('detail.universityName')
                        ->title('University Name')
                        ->placeholder('University of Dar es Salaam')
                        ->required(),

                    Input::make('detail.collegeName')
                        ->title('College Name')
                        ->placeholder('College of Engineering and Technology')
                        ->required(),

                    Input::make('detail.departmentName')
                        ->title('Department Name')
                        ->placeholder('Department of Computer Science')
                        ->required(),

                    Input::make('detail.degreeProgrammeName')
                        ->title('Degree Programme Name')
                        ->placeholder('Bachelor of Science in Computer Science')
                        ->required(),
                ])
            ])->title('Create New Detail')->applyButton('Create'),

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
        // delete user events
        $hostedEvents = EventHost::where('user_id', $user->id)->get();
        foreach ($hostedEvents as $event) {
            $event->delete();
        }

        // delete user projects
        $projects = ProjectOwner::where('user_id', $user->id)->get();
        foreach ($projects as $project) {
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

        $user->delete()
            ? Alert::info('You have successfully deleted the member.')
            : Alert::error('An error has occurred');

        return redirect()->route('platform.members');
    }

    public function createDetail(Request $request)
    {
        $university = University::where('name', $request->get('detail')['universityName'])->first();

        if (!$university) {
            $university = new University();
            $university->name = $request->get('detail')['universityName'];
            $university->save();
        }

        $college = College::where('name', $request->get('detail')['collegeName'])->where('university_id', $university->id)->first();

        if (!$college) {
            $college = new College();
            $college->name = $request->get('detail')['collegeName'];
            $college->university_id = $university->id;
            $college->save();
        } else if ($college->university_id != $university->id) {
            $college = new College();
            $college->name = $request->get('detail')['collegeName'];
            $college->university_id = $university->id;
            $college->save();
        }

        $department = Department::where('name', $request->get('detail')['departmentName'])->first();

        if (!$department) {
            $department = new Department();
            $department->name = $request->get('detail')['departmentName'];
            $department->college_id = $college->id;
            $department->save();
        } else if ($department->college_id != $college->id) {
            $department = new Department();
            $department->name = $request->get('detail')['departmentName'];
            $department->college_id = $college->id;
            $department->save();
        }

        $degreeProgramme = DegreeProgramme::where('name', $request->get('detail')['degreeProgrammeName'])->first();

        if (!$degreeProgramme) {
            $degreeProgramme = new DegreeProgramme();
            $degreeProgramme->name = $request->get('detail')['degreeProgrammeName'];
            $degreeProgramme->department_id = $department->id;
            $degreeProgramme->save();
        } else if ($degreeProgramme->department_id != $department->id) {
            $degreeProgramme = new DegreeProgramme();
            $degreeProgramme->name = $request->get('detail')['degreeProgrammeName'];
            $degreeProgramme->department_id = $department->id;
            $degreeProgramme->save();
        }
    }
}
