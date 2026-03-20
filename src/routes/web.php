<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;


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

//「 / にアクセスしたら誰でもログインページに飛ばす」ログイン済みでも / にアクセスしたらloginページに。
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {

    Route::resource('categories', CategoryController::class);
    Route::get('/tasks/search', [TaskController::class, 'search'])->name('tasks.search');//順番！
    Route::get('/tasks.sort', [TaskController::class, 'sort'])->name('tasks.sort');
    Route::resource('tasks', TaskController::class);
});
