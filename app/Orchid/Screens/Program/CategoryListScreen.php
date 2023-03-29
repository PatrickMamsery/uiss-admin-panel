<?php

namespace App\Orchid\Screens\Program;

use Orchid\Screen\Screen;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;

use App\Models\ProgramCategory as Category;
use App\Orchid\Layouts\Program\CategoryListLayout;
use Orchid\Support\Facades\Toast;
use Orchid\Support\Facades\Alert;

class CategoryListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Categories';

    public $description = "All program categories";

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'categories' => Category::paginate()
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
            ModalToggle::make('Create Category')
                ->modal('createCategoryModal')
                ->method('saveCategory')
                ->icon('plus')
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
                        ->placeholder('Name')
                        ->help('Name of the category'),

                    Input::make('category.id')
                        ->type('hidden')
                        ->required()
                        ->canSee(false),
                ])
            ])->async('asyncGetCategory')->title('Create Category'),

            CategoryListLayout::class
        ];
    }

    /**
     * @param Category $category
     *
     * @return array
     */
    public function asyncGetCategory(Category $category): array
    {
        return [
            'category' => $category,
        ];
    }

    public function saveCategory(Category $category = null, Request $request)
    {
        $request->validate([
            'category.name' => 'required'
        ]);

        $category->fill($request->input('category'))
            ->save();

        Toast::info('Category saved successfully');
        
        return redirect()->route('platform.program-categories');
    }
}
