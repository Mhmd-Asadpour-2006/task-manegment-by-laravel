<?php

use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\User\UserController;
use App\Models\User;
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
    
    Route::post('/',[UserController::class,'admin_login']);
    // Route::post('/register',[UserController::class,'admin_register']);

    Route::middleware(['auth:sanctum','can:admin'])->get('/getAdmins',[UserController::class,'get_all_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/add',[UserController::class,'add_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/remove',[UserController::class,'remove_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/update',[UserController::class,'update_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->get('/getUsers',[UserController::class,'get_all_user_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/addUser',[UserController::class,'add_user_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/removeUser',[UserController::class,'remove_user_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/updateUser',[UserController::class,'update_user_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->get('/getCategories',[UserController::class,'get_categories_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/addCategory',[UserController::class,'add_Category_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/removeCategory',[UserController::class,'remove_category_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/updateCategory',[UserController::class,'update_category_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->get('/getTasks',[UserController::class,'get_tasks_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/addTask',[UserController::class,'add_task_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/removeTask',[UserController::class,'remove_task_admin']);

    Route::middleware(['auth:sanctum','can:admin'])->post('/updateTask',[UserController::class,'update_task_admin']);

});


Route::middleware('auth:sanctum')->get('/getAll/categories', [HomeController::class,'getAllCategories']);

Route::middleware('auth:sanctum')->get('/logout', [UserController::class,'logout']);

Route::post('/login',[UserController::class,'login'] );//->withoutMiddleware('web');

Route::post('/register',[UserController::class,'register']);//->withoutMiddleware('web');

Route::fallback([HomeController::class,'not_found']);
