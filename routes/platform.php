<?php

declare(strict_types=1);

use Tabuna\Breadcrumbs\Trail;
use Illuminate\Support\Facades\Route;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\FAQs\FAQsEditScreen;
use App\Orchid\Screens\FAQs\FAQsListScreen;
use App\Orchid\Screens\News\NewsEditScreen;
use App\Orchid\Screens\News\NewsListScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\Event\EventEditScreen;
use App\Orchid\Screens\Event\EventListScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use App\Orchid\Screens\Program\ProgramEditScreen;
use App\Orchid\Screens\Program\ProgramListScreen;
use App\Orchid\Screens\Project\ProjectEditScreen;
use App\Orchid\Screens\Project\ProjectListScreen;
use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Home > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Profile'), route('platform.profile'));
    });

// Home > System > Users
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit');

// Home > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.systems.users')
            ->push(__('Create'), route('platform.systems.users.create'));
    });

// Home > System > Users > User
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Users'), route('platform.systems.users'));
    });

// Home > System > Roles > Role
Route::screen('roles/{roles}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(function (Trail $trail, $role) {
        return $trail
            ->parent('platform.systems.roles')
            ->push(__('Role'), route('platform.systems.roles.edit', $role));
    });

// Home > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.systems.roles')
            ->push(__('Create'), route('platform.systems.roles.create'));
    });

// Home > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Roles'), route('platform.systems.roles'));
    });

// NEWS

//platform > news
Route::screen('news',NewsListScreen::class)
    ->name('platform.news')
    ->breadcrumbs(function(Trail $trail){
        return $trail
            ->parent('platform.index')
            ->push(__('News'),route('platform.news'));
    });

// Home > news > edit
Route::screen('news-edit/{news?}',NewsEditScreen::class)
    ->name('platform.news.edit')
    ->breadcrumbs(function(Trail $trail){
        return $trail
            ->parent('platform.news')
            ->push(__('Edit'),route('platform.news.edit'));
    });

// FAQ

// Home > Faqs
Route::screen('faqs',FAQsListScreen::class)
    ->name('platform.faqs')
    ->breadcrumbs(function (Trail $trail){
        return $trail
            ->parent('platform.index')
            ->push(__('Faqs'),route('platform.faqs'));
    });

// Home > Faqs > Edit
Route::screen('faq/{faq?}',FAQsEditScreen::class)
    ->name('platform.faqs.faq')
    ->breadcrumbs(function (Trail $trail){
        return $trail
            ->parent('platform.faqs')
            ->push(__('Edit'),route('platform.faqs.faq'));
    });

// Programs

// Home > Programs
Route::screen('programs', ProgramListScreen::class)
    ->name('platform.programs')
    ->breadcrumbs(function (Trail $trail){
        return $trail
            ->parent('platform.index')
            ->push(__('Programs'), route('platform.programs'));
    });

// Home > Programs > Edit
Route::screen('program-edit/{program?}', ProgramEditScreen::class)
    ->name('platform.program.edit')
    ->breadcrumbs(function (Trail $trail){
        return $trail
            ->parent('platform.programs')
            ->push(__('Edit'), route('platform.program.edit'));
    });

// Events

// Home > Events
Route::screen('events', EventListScreen::class)
    ->name('platform.events');

// Home > Events > Edit
Route::screen('event/{event?}', EventEditScreen::class)
    ->name('platform.event.edit');



//Projects

// Home > Projects
Route::screen('project', ProjectListScreen::class)
    ->name('platform.projects');

// Home > Projects > Edit
Route::screen('proect/{project?}', ProjectEditScreen::class)
    ->name('platform.project.edit');


