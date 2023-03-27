<?php

namespace App\Orchid\Screens\Program;

use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;

use App\Models\Program;
use App\Orchid\Layouts\Program\ProgramListLayout;
use Orchid\Support\Facades\Toast;

class ProgramListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Programs';
    public $description = "All Programs";

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'programs' => Program::paginate()
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
            Link::make('Create new')
                ->icon('pencil')
                ->route('platform.program.edit', null)
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
            ProgramListLayout::class
        ];
    }

    public function remove(Request $request): void
    {
        Program::findOrFail($request->get('id'))
            ->delete();

        Toast::info(__('Program was deleted successfully'));
    }
}
