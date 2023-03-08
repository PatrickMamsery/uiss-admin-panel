<?php

namespace App\Orchid\Screens\Program;

use Orchid\Screen\Screen;
use App\Models\Program;

use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Quill;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Alert;

class ProgramEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Create Program';

    public $description = 'Create program';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Program $program): array
    {
        $this->exists = $program->exists;

        if ($this->exists) {
            $this->description = 'Update program';
        }

        return [
            'program' => $program
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
                Input::make('program.name')
                    ->title('Name')
                    ->required()
                    ->placeholder('Enter program name')
                    ->help('Enter program name'),

                TextArea::make('program.description')
                    ->title('Description')
                    ->rows(5)
                    ->required()
                    ->placeholder('Enter program description')
                    ->help('Enter program description')
                    ->help('What is the program about?'),
            ])
        ];
    }

    public function createOrUpdate(Program $program,Request $request )
    {


        $program->fill($request->get('program'))->save();

        Alert::info('Program is created successfully');

        return redirect()->route('platform.programs');
    }


    public function delete(Program $program)
    {
        $program->delete();

        Alert::info('You have successfully deleted a program.');

        return redirect()->route('platform.programs');
    }
}
