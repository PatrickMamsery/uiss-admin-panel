<?php

namespace App\Orchid\Screens\Program;

use Orchid\Screen\Screen;

class CategoryProgramsListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'CategoryProgramsListScreen';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [];
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
        return [];
    }
}
