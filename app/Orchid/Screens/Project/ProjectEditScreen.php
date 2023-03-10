<?php

namespace App\Orchid\Screens\Project;

use App\Models\Project;
use Orchid\Screen\Screen;
use Illuminate\Http\Request;
use App\Models\ProjectCategory;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Cropper;
use Orchid\Support\Facades\Alert;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Layout;

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
            $this->description = 'Update project';
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
                Input::make('project.name')
                    ->title('Name')
                    ->required()
                    ->placeholder('Enter project name'),

                TextArea::make('project.description')
                    ->title('Description')
                    ->rows(5)
                    ->required()
                    ->placeholder('Enter project description')
                    ->help('Enter project description'),

                Relation::make('project.category_id')
                    ->title('Category Id')
                    ->fromModel(ProjectCategory::class, 'id'),

                Cropper::make('project.image')
                    ->targetId()
                    ->title('Project Image')
                    ->width(1000)
                    ->height(500),

            ])
        ];
    }

    public function createOrUpdate(Project $project,Request $request )
    {


        $project->fill($request->get('project'))->save();

        Alert::info('event is created successfully');

        return redirect()->route('platform.projects');
    }


    public function delete(Project $project)
    {
        $project->delete();

        Alert::info('You have successfully deleted an event.');

        return redirect()->route('platform.projects');
    }
}
