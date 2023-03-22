<?php

namespace App\Orchid\Layouts;

// use Orchid\Screen\Layout;
use Orchid\Screen\Layouts\Listener;
use Orchid\Support\Facades\Layout;

use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Group;

use App\Models\University;
use App\Models\College;
use App\Models\Department;
use App\Models\DegreeProgramme;
use App\Models\MemberDetail;

class ExtraDetailsListener extends Listener
{
    /**
     * List of field names for which values will be listened.
     *
     * @var string[]
     */
    protected $targets = [
        'university_id',
        'college_id',
        'department_id',
        // 'user.memberDetail.degree_programme_id',
    ];

    /**
     * What screen method should be called
     * as a source for an asynchronous request.
     *
     * The name of the method must
     * begin with the prefix "async"
     *
     * @var string
     */
    protected $asyncMethod = 'asyncGetExtraDetails';

    /**
     * @return Layout[]
     */
    protected function layouts(): array
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
                ]),

                Group::make([
                    Input::make('member.reg_no')
                        ->title('Registration Number')
                        ->required()
                        ->placeholder('2019/0001')
                        ->help('Enter the registration number of the member'),
                    
                    Input::make('member.area_of_interest')
                        ->title('Area of Interest')
                        ->required()
                        ->placeholder('Software Development')
                        ->help('Enter the area of interest of the member - not more than 100 characters'),
                ]),

                Group::make([
                    Select::make('member.university_id')
                        ->title('University')
                        // ->onChange('getExtraDetails')
                        ->fromModel(University::class, 'name')
                        ->empty('Select a university')
                        ->required()
                        ->help('Select the university you are currently studying in.'),

                    Select::make('member.college_id')
                        ->title('College')
                        ->fromModel(College::class, 'name')
                        ->empty('Select a college')
                        // ->canSee($this->query->has('colleges'))
                        // ->options($this->query->get('colleges'))
                        ->required()
                        ->help('Select the college you are currently studying in.'),

                    Select::make('member.department_id')
                        ->title('Department')
                        ->fromModel(Department::class, 'name')
                        ->empty('Select a department')
                        ->required()
                        ->help('Select the department you are currently studying in.'),

                    Select::make('member.degree_programme_id')
                        ->title('Degree Programme')
                        ->fromModel(DegreeProgramme::class, 'name')
                        ->empty('Select a degree programme')
                        ->required()
                        ->help('Select the degree programme you are currently studying in.'),
                ])
            ])
        ];
    }
}
