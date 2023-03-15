<?php

namespace App\Orchid\Layouts\Members;

use App\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class RegisteredListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'users';

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
                        ->route('platform.members.edit', $user);
                }),

            TD::make('email', 'Email')
                ->sort()
                ->filter(TD::FILTER_TEXT),

            TD::make('created_at', 'Registered')
                ->sort()
                ->filter(TD::FILTER_DATE)
                ->render(function (User $user) {
                    return $user->created_at->format('d.m.Y');
                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (User $user) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.members.edit', $user)
                                ->icon('pencil'),

                            Button::make(__('Promote'))
                                ->method('promote')
                                ->confirm(__('Are you sure you want to promote the user to a leader role?'))
                                ->parameters([
                                    'id' => $user->id,
                                ])
                                ->icon('briefcase'),

                            Button::make(__('Delete'))
                                ->method('remove')
                                ->confirm(__('Are you sure you want to delete the user?'))
                                ->parameters([
                                    'id' => $user->id,
                                ])
                                ->icon('trash'),

                            // Button::make(__('Reset Password'))
                            //     ->method('resetPassword')
                            //     ->confirm(__('Are you sure you want to reset the password?'))
                            //     ->parameters([
                            //         'id' => $user->id,
                            //     ])
                            //     ->icon('key'),
                        ]);
                }),
        ];
    }
}
