<?php

namespace App\Orchid\Layouts\Project;

use App\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ProjectOwnersListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'owners';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('name', 'Name')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (User $user) {
                    return Link::make($user->name)
                        ->route('platform.project-owner.projects', $user);
                }),

            TD::make('projects', '# of Projects')->render(function (
                User $user
            ) {
                return $user->owns->count();
            }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (User $user) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            ModalToggle::make(__('Edit'))
                                ->icon('pencil')
                                ->modal('editProjectOwnerDetails')
                                ->method('editProjectOwner')
                                ->asyncParameters([
                                    'user' => $user->id,
                                ]),
                        ]);
                }),
        ];
    }
}
