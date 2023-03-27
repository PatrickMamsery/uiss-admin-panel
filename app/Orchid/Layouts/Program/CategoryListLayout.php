<?php

namespace App\Orchid\Layouts\Program;

use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;

use App\Models\ProgramCategory as Category;
use Illuminate\Support\Str;

class CategoryListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'categories';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('id','ID')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function(Category $category){
                    return $category->id;
                }),

            TD::make('name','Name')
                ->render(function(Category $category){
                    return ModalToggle::make(Str::ucfirst($category->name))
                        ->modal('createCategoryModal')
                        ->modalTitle($category->name)
                        ->method('saveCategory')
                        ->asyncParameters([
                            'category' => $category->id,
                        ]);
                }),

            TD::make('programs', '# of Programs')
                ->render(function(Category $category){
                    return $category->programs->count();
                }),

            TD::make('Actions')
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Category $category) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            ModalToggle::make('Edit')
                                ->modal('createCategoryModal')
                                ->modalTitle($category->name)
                                ->method('saveCategory')
                                ->asyncParameters([
                                    'category' => $category->id,
                                ])
                                ->icon('pencil'),

                            Button::make('Delete')
                                ->method('remove')
                                ->confirm('Are you sure you want to delete the category?')
                                ->parameters([
                                    'id' => $category->id,
                                ])
                                ->icon('trash'),
                        ]);
                }),
        ];
    }
}
