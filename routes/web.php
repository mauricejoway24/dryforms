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

//Auth::routes();
Route::middleware(['auth'])->namespace('Backend')->prefix('admin')->group(function() {
    Route::resource('users', 'UsersController');
    Route::resource('plans', 'PlansController');
    Route::resource('coupons', 'CouponsController');
    Route::resource('standard/forms', 'StandardFormsController');
    Route::resource('standard/scopes', 'StandardScopesController');
    Route::get('standard/moisture', 'StandardMoistureMapController@index')->name('moisture-page');
    Route::get('standard/area', 'StandardAffectedAreasController@areaPage')->name('areas-page');
    Route::get('standard/team', 'StandardTeamsController@teamsPage')->name('teams-page');
    Route::resource('units-of-measure', 'UnitsOfMeasureController');
    Route::get('notifications/auto-responders/{id}/preview', 'AutoRespondersController@preview')->name('auto-responders.preview');
    Route::resource('notifications/auto-responders', 'AutoRespondersController');

// ------------------API-----------------//
    Route::resource('standard/areas', 'StandardAffectedAreasController');
    Route::resource('standard/teams', 'StandardTeamsController');
    Route::resource('standard/structure', 'StardardMoistureMapStructureController');
    Route::resource('standard/material', 'StardardMoistureMapMaterialController');
    Route::get('uoms/jsonResult', 'UnitsOfMeasureController@jsonResult');
    Route::get('standard-scopes/form', 'StandardScopesController@form');
    Route::post('standard-scopes/form-update', 'StandardScopesController@formUpdate');
    Route::get('standard-moisture/form', 'StandardMoistureMapController@form');
    Route::post('standard-moisture/form-update', 'StandardMoistureMapController@formUpdate');
    Route::get('standard-areas/form', 'StandardAffectedAreasController@form');
    Route::post('standard-areas/form-update', 'StandardAffectedAreasController@formUpdate');

    Route::resource('training/categories', 'TrainingController');
    Route::resource('training/videos', 'TrainingVideosController');

    Route::get('ticket/{ticket_id}', 'TicketsController@show');
    Route::post('comment_ticket', 'TicketCommentsController@postComment');
    Route::get('tickets/list', 'TicketsController@index');
    Route::post('close_ticket/{ticket_id}', 'TicketsController@close');
    Route::resource('ticket_categories', 'TicketCategoriesController');
});

Route::get('/project/print/{id}', 'Api\ProjectFormsController@print');
Route::get('/form/preview/{token}', 'Api\StandardsController@preview');
Route::get('/dropbox-auth', 'Api\CompaniesController@dropboxAuth');
Route::post('/dropbox-auth', 'Api\CompaniesController@storeDropboxAuth');

Route::middleware([])->namespace('Frontend')->group(function() {
    Route::get('/', ['uses' => 'HomeController@index', 'as' => 'main.index']);
});
