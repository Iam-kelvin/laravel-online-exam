<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\ExamPresetController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportExportController;
use App\Models\ExamPreset;
use App\Models\Question;
use App\Models\Subject;

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
    return view('welcome', [
        'stats' => [
            'subjects' => Subject::where('active', true)->count(),
            'questions' => Question::count(),
            'presets' => ExamPreset::where('active', true)->count(),
        ],
    ]);
});

Auth::routes(['verify' => true]);

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/account/settings', [AccountController::class, 'edit'])->name('account.edit');
    Route::patch('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::patch('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
});

Route::group(['namespace' => 'App\Http\Controllers\Admin', 'prefix' => 'admin', 'middleware' => ['auth', 'can:manage-users']], function(){
    Route::resource('users', UsersController::class, ['except' => ['show','create','store']]);
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'can:recover-user-email']], function () {
    Route::get('/users/{user}/recover-email', [UsersController::class, 'editEmail'])->name('users.email.edit');
    Route::patch('/users/{user}/recover-email', [UsersController::class, 'updateEmail'])->name('users.email.update');
});

// Routes for admin panel
Route::group(['namespace' => 'App\Http\Controllers\Admin', 'middleware' => ['auth', 'can:manage-questions'], 'prefix' => 'admin'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('questions', QuestionController::class);
    Route::resource('subjects', SubjectController::class)->except(['create', 'show']);
    Route::resource('exam-presets', ExamPresetController::class)
        ->parameters(['exam-presets' => 'examPreset'])
        ->except(['create', 'show']);
});

// Routes for exams and results
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/exam/start', [ExamController::class, 'start'])->name('exam.start');
    Route::post('/exam/start', [ExamController::class, 'store'])->name('exam.store');
    Route::get('/exam/attempts/{attempt}', [ExamController::class, 'take'])->name('exam.take');
    Route::post('/exam/attempts/{attempt}/submit', [ExamController::class, 'submit'])->name('exam.submit');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/exam/results', [ExamController::class, 'results'])->name('exam.results');
});

/*Excel import export*/
Route::get('importExportView', [ImportExportController::class, 'importExportView'])->middleware(['auth', 'can:manage-questions']);
Route::post('import', [ImportExportController::class, 'import'])->name('import')->middleware(['auth', 'can:manage-questions']);
