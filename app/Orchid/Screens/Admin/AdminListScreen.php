<?php

namespace App\Orchid\Screens\Admin;

use Orchid\Screen\Screen;

class AdminListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'AdminListScreen';

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
