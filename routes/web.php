<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['namespace' => 'App\Http\Controllers\Admin', 'prefix' => 'admin', 'name' => 'admin.', 'middleware' => 'can:manage-users', 'can:manage-questions'], function(){
    Route::resource('users', UsersController::class, ['except' => ['show','create','store']]);
    // Route::resource('questions', QuestionController::class);
});

// Routes for admin panel
Route::group(['namespace' => 'App\Http\Controllers\Admin', 'middleware' => ['auth', 'can:manage-questions'], 'prefix' => 'admin'], function () {
    Route::resource('questions', QuestionController::class);
});

// Routes for exams and results
Route::middleware(['auth'])->group(function () {
    Route::get('/exam/start', [App\Http\Controllers\ExamController::class, 'start'])->name('exam.start');
    Route::post('/exam/submit', [App\Http\Controllers\ExamController::class, 'submit'])->name('exam.submit');
    Route::get('/exam/results', [App\Http\Controllers\ExamController::class, 'results'])->name('exam.results');
});

/*Excel import export*/
Route::get('export', 'App\Http\Controllers\ImportExportController@export')->name('export')->middleware('can:manage-questions');
Route::get('importExportView', 'App\Http\Controllers\ImportExportController@importExportView')->middleware('can:manage-questions');
Route::post('import', 'App\Http\Controllers\ImportExportController@import')->name('import')->middleware('can:manage-questions');

