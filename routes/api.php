<?php
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/run-migrations-and-seed', function () {
    // Artisan::call('migrate', ["--force" => true]);
    Artisan::call('migrate:fresh', ["--force" => true]);
    Artisan::call('db:seed', ["--force" => true]);
    return 'Migrations and Seed completed successfully!';
});
Route::post('login', [App\Http\Controllers\Auth\CredentialController::class, 'onLogin']);
Route::post('personal/information/create', [App\Http\Controllers\UserProfile\PersonalInformationController::class, 'onCreate']); // Register
Route::group(['middleware' => ['auth:sanctum']], function () {

    #region Authentication
    Route::get('logout', [App\Http\Controllers\Auth\CredentialController::class, 'onLogout']);
    Route::post('change/password', [App\Http\Controllers\Auth\CredentialController::class, 'onChangePassword']);
    Route::post('change/status', [App\Http\Controllers\Auth\CredentialController::class, 'onChangeStatus']);
    #endregion

    #region Personal Information
    Route::post('personal/information/update/{id}', [App\Http\Controllers\UserProfile\PersonalInformationController::class, 'onUpdateById']);
    Route::post('personal/information/status/{id}', [App\Http\Controllers\UserProfile\PersonalInformationController::class, 'onChangeStatus']);
    Route::get('personal/information/all', [App\Http\Controllers\UserProfile\PersonalInformationController::class, 'onGetAll']);
    Route::get('personal/information/get/{id}', [App\Http\Controllers\UserProfile\PersonalInformationController::class, 'onGetById']);
    Route::post('personal/information/get', [App\Http\Controllers\UserProfile\PersonalInformationController::class, 'onGetPaginatedList']);
    Route::post('personal/information/delete/{id}', [App\Http\Controllers\UserProfile\PersonalInformationController::class, 'onDeleteById']);
    #endregion

    #region Access Management
    Route::post('configuration/access/create', [App\Http\Controllers\SystemConfiguration\AccessManagementController::class, 'onCreate']);
    Route::post('configuration/access/update/{id}', [App\Http\Controllers\SystemConfiguration\AccessManagementController::class, 'onUpdateById']);
    Route::put('configuration/access/status/{id}', [App\Http\Controllers\SystemConfiguration\AccessManagementController::class, 'onChangeStatus']);
    Route::get('configuration/access/all', [App\Http\Controllers\SystemConfiguration\AccessManagementController::class, 'onGetAll']);
    Route::get('configuration/access/get/{id}', [App\Http\Controllers\SystemConfiguration\AccessManagementController::class, 'onGetById']);
    Route::get('configuration/access/settings', [App\Http\Controllers\SystemConfiguration\AccessManagementController::class, 'onGetAllConfiguration']);
    Route::delete('configuration/access/delete/{id}', [App\Http\Controllers\SystemConfiguration\AccessManagementController::class, 'onDeleteById']);
    #endregion
});
