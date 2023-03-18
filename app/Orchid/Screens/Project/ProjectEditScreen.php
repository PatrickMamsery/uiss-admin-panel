<?php

namespace App\Orchid\Screens\Project;

use App\Models\Project;
use Orchid\Screen\Screen;
use Illuminate\Http\Request;
use App\Models\ProjectCategory;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Group;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Toast;
use Orchid\Support\Facades\Layout;

use Orchid\Screen\Actions\ModalToggle;

use App\Models\ProjectCategory as Category;
use App\Models\ProjectOwner as Owner;
use App\Models\User;

class ProjectEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Create Project';
    public $description = 'Create project';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Project $project): array
    {
        $this->exists = $project->exists;

        if ($this->exists) {
            $this->description = 'Update project details';
        }

        return [
            'project' => $project
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
                ->canSee($this->exists),

            ModalToggle::make('Add Category')
                ->modal('createCategoryModal')
                ->method('createCategory')
                ->icon('plus')
                ->canSee(!$this->exists),
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
            Layout::modal('createCategoryModal', [
                Layout::rows([
                    Input::make('category.name')
                        ->title('Name')
                        ->required()
                        ->placeholder('Category')
                        ->help('Specify the name/title of the category.'),
                ])
            ])->title('Create new category'),

            Layout::rows([
                Group::make([
                    Input::make('project.title')
                        ->title('Title')
                        ->required()
                        ->placeholder('Enter project title'),

                    Select::make('project.category_id')
                        ->fromModel(ProjectCategory::class, 'name')
                        ->title('Category')
                        ->required()
                        ->help('Select category to which this project belongs'),

                    Input::make('project.owner')
                        ->title('Project Owner\'s Full Name')
                        ->required()
                        ->placeholder('Full name'),
                ]),

                Quill::make('project.description')
                    ->title('Description')
                    ->required()
                    ->placeholder('Enter project description')
                    ->help('Enter project description'),


                Cropper::make('project.image')
                    ->targetUrl()
                    ->title('Project Image')
                    ->required(),

            ])
        ];
    }

    public function createCategory(Request $request)
    {
        $category = Category::create($request->get('category'));

        Toast::info('Category is created successfully');

        return redirect()->route('platform.project.edit');
    }

    public function createOrUpdate(Project $project, Request $request)
    {
        $project->fill($request->get('project'))->save();

        // create a new entry in project owner's table
        $ownerName = $request->get('project')['owner'];

        $user = User::where('name', $ownerName)->first();

        if (is_null($user)) {
            $user = User::create([
                'name' => $ownerName,
                'email' => $ownerName . '@example.com',
                'password' => bcrypt($ownerName),
                'role_id' => 5, // assign as a normal member
                'isProjectOwner' => 1
            ]);
        } else {
            $user->update(['isProjectOwner' => 1]);
        }

        $owner = Owner::firstOrCreate(
            [
                'project_id' => $project->id,
                'user_id' => $user->id,
            ],
        );

        $project->attachment()->syncWithoutDetaching(
            $request->input('project.attachment', [])
        );

        Alert::info('Project is created successfully');

        return redirect()->route('platform.projects');
    }


    public function delete(Project $project)
    {
        $project->delete();

        Alert::info('You have successfully deleted an event.');

        return redirect()->route('platform.projects');
    }
}
