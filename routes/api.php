<?php

use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\TaskController;
use App\Models\Categories;
use App\Models\User;
use Illuminate\Console\View\Components\Task;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/', [HomeController::class,'index']);

Route::prefix('task')->group(function (){

    Route::middleware('auth:sanctum')->post('/add',[HomeController::class,'add_task']);

    Route::middleware('auth:sanctum')->post('/remove',[HomeController::class,'remove_task']);

    Route::middleware('auth:sanctum')->post('/update/{task_id}',[HomeController::class,'update_task']);

});

Route::middleware('auth:sanctum')->post('/category/add', [HomeController::class,'add_category']);

Route::middleware('auth:sanctum')->post('/category/remove', [HomeController::class,'remove_category']);

Route::middleware('auth:sanctum')->post('/category/update', [HomeController::class,'update_category']);

Route::prefix('admin')->group(function (){
    
    Route::post('/',[AdminController::class,'admin_login']);
    // Route::post('/register',[UserController::class,'admin_register']);

    Route::middleware(['auth:sanctum','can:admin'])->get('/getAdmins',[AdminController::class,'get_all_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/add',[AdminController::class,'add_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/remove',[AdminController::class,'remove_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/update',[AdminController::class,'update_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->get('/getUsers',[AdminController::class,'get_all_user_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/addUser',[AdminController::class,'add_user_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/removeUser',[AdminController::class,'remove_user_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/updateUser',[AdminController::class,'update_user_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->get('/getCategories',[CategoriesController::class,'get_categories_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/addCategory',[CategoriesController::class,'add_Category_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/removeCategory',[CategoriesController::class,'remove_category_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/updateCategory',[CategoriesController::class,'update_category_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->get('/getTasks',[TaskController::class,'get_tasks_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/addTask',[TaskController::class,'add_task_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/removeTask',[TaskController::class,'remove_task_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/updateTask',[TaskController::class,'update_task_admin']);

});


Route::middleware('auth:sanctum')->get('/getAll/categories', [HomeController::class,'getAllCategories']);

Route::middleware('auth:sanctum')->get('/logout', [UserController::class,'logout']);

Route::post('/login',[UserController::class,'login'] );//->withoutMiddleware('web');

Route::post('/register',[UserController::class,'register']);//->withoutMiddleware('web');

Route::fallback([HomeController::class,'not_found']);
