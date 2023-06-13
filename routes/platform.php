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
use App\Orchid\Screens\Event\EventHostsListScreen;
use App\Orchid\Screens\Event\HostEventsListScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use App\Orchid\Screens\Program\ProgramEditScreen;
use App\Orchid\Screens\Program\ProgramListScreen;
use App\Orchid\Screens\Program\CategoryListScreen;
use App\Orchid\Screens\Project\ProjectEditScreen;
use App\Orchid\Screens\Project\ProjectListScreen;
use App\Orchid\Screens\Project\ProjectOwnersListScreen;
use App\Orchid\Screens\Project\OwnersProjectsListScreen;
use App\Orchid\Screens\Admin\AdminListScreen;
use App\Orchid\Screens\Admin\AdminEditScreen;
use App\Orchid\Screens\Members\RegisteredListScreen;
use App\Orchid\Screens\Members\RegisteredEditScreen;
use App\Orchid\Screens\Members\LeadersListScreen;
use App\Orchid\Screens\Members\LeadersEditScreen;
use App\Orchid\Screens\Image\ImageEditScreen;
use App\Orchid\Screens\Image\AlbumImageListScreen;
use App\Orchid\Screens\Image\AlbumListScreen;
use App\Orchid\Screens\Image\AlbumEditScreen;
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


// Home > Program Categories
Route::screen('program-categories', CategoryListScreen::class)
    ->name('platform.program-categories')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Program Categories'), route('platform.program-categories'));
    });

// Events

// Home > Events
Route::screen('events', EventListScreen::class)
    ->name('platform.events')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Events'), route('platform.events'));
    });

// Home > Events > Edit
Route::screen('event/{event?}', EventEditScreen::class)
    ->name('platform.event.edit')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.events')
            ->push(__('Edit'), route('platform.event.edit'));
    });

// Home > Event Hosts
Route::screen('event-hosts', EventHostsListScreen::class)
    ->name('platform.event-hosts')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Event Hosts'), route('platform.event-hosts'));
    });

// Home > Event Host > Projects
Route::screen('events-by-host/{user?}', HostEventsListScreen::class)
    ->name('platform.event-host.events')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.event-hosts')
            ->push(__('Events'), route('platform.event-host.events'));
    });



//Projects

// Home > Projects
Route::screen('projects', ProjectListScreen::class)
    ->name('platform.projects')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Projects'), route('platform.projects'));
    });

// Home > Projects > Edit
Route::screen('project/{project?}', ProjectEditScreen::class)
    ->name('platform.project.edit')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.projects')
            ->push(__('Edit'), route('platform.project.edit'));
    });

// Home > Project Owners
Route::screen('project-owners', ProjectOwnersListScreen::class)
    ->name('platform.project-owners')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Project Owners'), route('platform.project-owners'));
    });

// Home > Project Owners > Projects
Route::screen('projects-by-owner/{user?}', OwnersProjectsListScreen::class)
    ->name('platform.project-owner.projects')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.project-owners')
            ->push(__('Projects'), route('platform.project-owner.projects'));
    });

// Home > Members
Route::screen('members', RegisteredListScreen::class)
    ->name('platform.members')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Members'), route('platform.members'));
    });

// Home > Members > Edit
Route::screen('member/{member?}', RegisteredEditScreen::class)
    ->name('platform.members.edit')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.members')
            ->push(__('Edit'), route('platform.members.edit'));
    });

// Home > Leaders
Route::screen('leaders', LeadersListScreen::class)
    ->name('platform.leaders')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Leaders'), route('platform.leaders'));
    });

// Home > Leaders > Edit
Route::screen('leader/{leader?}', LeadersEditScreen::class)
    ->name('platform.leaders.edit')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.leaders')
            ->push(__('Edit'), route('platform.leaders.edit'));
    });

// Home > Admin Users
Route::screen('admin-users', AdminListScreen::class)
    ->name('platform.admin-users')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Admin Users'), route('platform.admin-users'));
    });

// Home > Admin Users > Edit
Route::screen('admin-user/{admin?}', AdminEditScreen::class)
    ->name('platform.admin-user.edit')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.admin-users')
            ->push(__('Edit'), route('platform.admin-user.edit'));
    });


// Home > Image > Edit
Route::screen('image-edit/{image?}', ImageEditScreen::class)
->name('platform.image.edit')
->breadcrumbs(function (Trail $trail){
    return $trail
        ->parent('platform.album_images')
        ->push(__('Edit'),route('platform.image.edit'));
});

// Home > Albums > Images
Route::screen('album_images/{album?}', AlbumImageListScreen::class)
->name('platform.album_images')
->breadcrumbs(function (Trail $trail){
    return $trail
        ->parent('platform.albums')
        ->push(__('Images'), route('platform.album_images'));
});

// Home > Albums
Route::screen('albums', AlbumListScreen::class)
->name('platform.albums')
->breadcrumbs(function (Trail $trail){
    return $trail
        ->parent('platform.index')
        ->push(__('Albums'),route('platform.albums'));
});

// Home > ImageAlbum > Edit
Route::screen('album-edit/{image?}', AlbumEditScreen::class)
->name('platform.album.edit')
->breadcrumbs(function (Trail $trail){
    return $trail
        ->parent('platform.album_images')
        ->push(__('Edit'),route('platform.album.edit'));
});


