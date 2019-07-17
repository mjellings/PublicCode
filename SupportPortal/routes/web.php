<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true]);

Route::get('/', 'HomeController@index')->name('home')->middleware('verified');

Route::get('/home', 'HomeController@index')->name('home.alt')->middleware('verified');

// User Routes
Route::namespace('User')->middleware(['verified'])->group(function () {
        Route::get('/dashboard', 'DashboardController@index')->name('user.dashboard');

        // Ticket routes
        Route::get( 'tickets', 'TicketController@index')->name('user.tickets.index');
        Route::get( 'tickets/create', 'TicketController@create')->name('user.tickets.create');
        Route::post('tickets/create', 'TicketController@store')->name('user.tickets.store');
        // Specific ticket (must be last)
        Route::get( 'tickets/{ref}', 'TicketController@show')->name('user.tickets.show');

        // Ticket updates
        Route::post('ticket-update/create', 'TicketUpdateController@store')->name('user.ticket-update.store');

        // Ticket File routes
        Route::get('ticket-file/{file}', 'TicketFileController@index')
                ->name('user.ticket-file.download');

        // Internal Ticket routes
        Route::get('/internal-tickets', 'InternalTicketController@index')
                ->name('user.internal-tickets.index');

        // Client routes
        Route::get('/clients', 'ClientController@index')
                ->name('user.clients.index');

        // User Profile
        Route::get('/profile', 'ProfileController@index')->name('user.profile');
});



// Admin Routes
Route::namespace('Admin')->prefix('admin')->middleware(['is_admin','verified'])->group(function () {
        // Dashboard
        Route::get('dashboard', 'DashboardController@index')->name('admin.dashboard');

        // Tickets
        Route::get('tickets', 'TicketController@index')->name('admin.tickets.index');
        Route::get('tickets/create/{client}/{user}', 'TicketController@create')->name('admin.tickets.create');
        Route::post('tickets/create/{client}/{user}', 'TicketController@store')->name('admin.tickets.store');
        // Specific Ticket (Must be last)
        Route::get('tickets/{ref}', 'TicketController@show')->name('admin.tickets.show');
        
        // Ticket Updates
        Route::post('ticket-update/create', 'TicketUpdateController@store')->name('admin.ticket-update.store');
        
        // Clients
        Route::get('clients', 'ClientController@index')->name('admin.clients.index');
        Route::get('clients/create', 'ClientController@create')->name('admin.clients.create');
        Route::get('clients/{client_key}', 'ClientController@show')->name('admin.clients.show');
        Route::post('clients/{client_key}/addUser', 'ClientController@addUser')->name('admin.clients.addUser');
        Route::post('clients/{client_key}/users', 'ClientController@updateUsers')->name('admin.clients.updateUsers');
        
        // Users
        Route::get('users', 'UserController@index')->name('admin.users.index');
        Route::get('users/{user}', 'UserController@show')->name('admin.users.show');
        
        // Search
        Route::get('search', 'SearchController@index')->name('admin.search');

        // Other
        Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
        Route::get('env/{env}', 'EnvironmentController@index')->name('admin.environment.index');
});