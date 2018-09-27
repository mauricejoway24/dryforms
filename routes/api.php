<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api'], function (Router $router) {
    $router->post('login', ['uses' => 'Auth\ApiAuthController@login', 'as' => 'api.login']);
    $router->post('register', ['uses' => 'Auth\ApiAuthController@register', 'as' => 'api.register']);
    $router->post('logout', 'Auth\ApiAuthController@logout');
    $router->get('refresh', 'Auth\ApiAuthController@refresh');
});

Route::namespace('Api')->middleware(['jwt.auth'])->group(function(Router $router) {
    $router->resource('companies', 'CompaniesController');
    $router->post('companies/{company}/logo', 'CompaniesController@storeLogo');
    $router->delete('companies/{company}/logo', 'CompaniesController@removeLogo');
    $router->resource('categories', 'EquipmentCategoriesController');
    $router->resource('models', 'EquipmentModelsController');
    $router->resource('teams', 'TeamsController');
    $router->resource('events', 'EventController');
    $router->resource('tickets', 'TicketsController');
    $router->get('ticket/categories', 'TicketsController@getCategories');
    $router->resource('ticket/comment', 'TicketCommentsController');
    $router->resource('statuses', 'EquipmentStatusesController');
    $router->resource('equipment', 'EquipmentsController');
    $router->resource('forms', 'FormsController');
    $router->resource('users', 'UsersController');
    $router->resource('roles', 'RolesController');
    $router->resource('uoms', 'UomsController');
    $router->resource('review-links', 'ReviewLinksController');
    $router->resource('review-request-message', 'ReviewRequestMessagesController');
    $router->resource('projects', 'ProjectsController');
    $router->get('project/company', 'ProjectsController@getCompany');
    $router->resource('project/status', 'ProjectStatusController');
    $router->resource('project/forms', 'ProjectFormsController');
    $router->resource('project/{project}/call-report', 'CallReportsController');
    $router->resource('project/dailylog', 'ProjectDailylogsController');
    $router->resource('project/statement', 'ProjectStatementsController');
    $router->post('project/statement/revert/{id}', 'ProjectStatementsController@revertStatement');
    $router->post('project-statement/check', 'ProjectStatementsController@setTitleAsSelected');
    $router->resource('project/area', 'ProjectAreasController');
    $router->resource('project/scope', 'ProjectScopesController');
    $router->resource('project/moisture', 'ProjectMoistureController');
    $router->resource('project/instrument', 'ProjectInstrumentsController');
    $router->patch('project/moisture/date/{id}', 'ProjectMoistureController@updateDate');
    $router->post('project/moisture/date', 'ProjectMoistureController@addDates');
    $router->resource('project/psychometric', 'ProjectPsychometricController');
    $router->resource('standard/forms', 'StandardsController');
    $router->resource('standard/form_orders', 'SidebarController');
    $router->resource('standard/scopes', 'StandardScopesController');
    $router->get('standard/scopes-revert', 'StandardScopesController@revert');
    $router->resource('standard/dailylog_settings', 'StandardDailylogSettingsController');
    $router->resource('areas', 'AreasController');
    $router->resource('standard/structures', 'StructuresController');
    $router->resource('standard/materials', 'MaterialsController');
    $router->get('standard/structures-revert', 'StructuresController@revert');
    $router->get('standard/materials-revert', 'MaterialsController@revert');
    $router->post('project/psychometric/update-time', 'ProjectPsychometricController@updateTime');
    $router->patch('project/psychometric/update-measurements/{id}', 'ProjectPsychometricController@updateMeasurements');
    $router->delete('project/psychometric/destroy-day/{id}', 'ProjectPsychometricController@destroyDay');
    $router->get('training/categories', 'TrainingManageController@index');
    $router->get('training/videos', 'TrainingManageController@getVideos');
    $router->post('project/email', 'ProjectFormsController@send');
    $router->get('project/email/send', 'ProjectFormsController@sendEmail')->name('send-email');
    $router->delete('equipments-bulk-delete', 'EquipmentsController@bulkDestroy');
    $router->get('get-models/{id}', 'EquipmentCategoriesController@getModels');
    $router->get('validate-serial/{serial}/category_id/{categoryId}', 'EquipmentsController@validateSerial');
    $router->post('standard/statement', ['uses' => 'StandardsController@statementStore', 'as' => 'standard.statement.store']);
    $router->delete('standard/statement/{id}', ['uses' => 'StandardsController@statementDelete', 'as' => 'standard.statement.delete']);

    $router->post('project/set-signature', 'ProjectFormsController@setSignature');
    $router->post('project/restore-status', 'ProjectsController@restoreStatus');

    $router->get('psychometric/calculate', 'PsychometricCalculationsController@calculate');
    $router->get('psychometric/dew', 'DewCalculationController@calculate');

    /** Account */
    $router->get('account', ['uses' => 'AccountController@show', 'as' => 'account.show']);
    $router->post('account/credit-card/update', 'AccountController@updateSource')->name('credit-card.update');
    $router->post('account/password/change', ['uses' => 'AccountController@changePassword', 'as' => 'account.password.change']);
    $router->post('account/email/change', ['uses' => 'AccountController@changeEmail', 'as' => 'account.email.change']);

    $router->post('account/subscription/cancel', ['uses' => 'AccountController@cancelSubscription', 'as' => 'account.subscription.cancel']);
    $router->get('account/subscription/resume', ['uses' => 'AccountController@resumeSubscription', 'as' => 'account.subscription.resume']);

/*------ testing code -------*/
    $router->post('account/subscribe', ['uses' => 'AccountController@subscribe', 'as' => 'account.subscribe.create']);
    $router->get('account/resume-subscribe', ['uses' => 'AccountController@resumeSubscription', 'as' => 'account.subscribe.resume']);
    $router->get('account/get-invoices', ['uses' => 'AccountController@getInvoices', 'as' => 'account.get.invoices']);

/*---------------------------*/
});
