<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminCheck;
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
Route::prefix('app/')->middleware([AdminCheck::class])->group(function(){
    Route::post('create_tag',[AdminController::class, 'addTag']);
    Route::post('edit_tag',[AdminController::class, 'editTag']);
    Route::post('delete_tag',[AdminController::class, 'deleteTag']);
    Route::post('upload',[AdminController::class, 'upload']);
    Route::post('delete_image',[AdminController::class, 'deleteImage']);

    Route::post('create_category',[AdminController::class, 'addCategory']);
    Route::post('edit_category',[AdminController::class, 'editCategory']);
    Route::post('delete_category',[AdminController::class, 'deleteCategory']);

    Route::post('create_role',[AdminController::class, 'addRole']);
    Route::post('edit_role',[AdminController::class, 'editRole']);
    Route::post('delete_role',[AdminController::class, 'deleteRole']);

    Route::post('create_adminuser',[AdminController::class, 'addAdminUser']);
    Route::post('edit_adminuser',[AdminController::class, 'editAdminUser']);
    Route::post('delete_adminuser',[AdminController::class, 'deleteAdminUser']);

    Route::get('get_tags',[AdminController::class, 'getTags']);
    Route::get('get_categories',[AdminController::class, 'getCategories']);
    Route::get('get_roles',[AdminController::class, 'getRoles']);
    Route::get('get_adminusers',[AdminController::class, 'getAdminUser']);

    Route::post('admin_login',[AdminController::class, 'adminLogin']);
    Route::post('assign_roles',[AdminController::class, 'assignRoles']);
});

Route::get('/',[AdminController::class, 'index']);

Route::get('/logout',[AdminController::class, 'logout']);
Route::any('{slug}',[AdminController::class, 'index']);


// Route::get('/', function () {
//     return view('welcome');
// });

// Route::any('{slug}',function(){
//     return view('welcome');
// });
