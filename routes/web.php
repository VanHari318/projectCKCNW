<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ProfileController;

// Home page
Route::get('/', function () {
    return view('auth.login');
});

// Test locale (for debugging)
Route::get('/test-locale', function () {
    return response()->json([
        'current_locale' => app()->getLocale(),
        'session_locale' => session()->get('locale'),
        'cookie_locale' => request()->cookie('locale'),
        'config_locale' => config('app.locale'),
        'locale_switch_url_en' => route('locale.switch', 'en'),
        'locale_switch_url_vi' => route('locale.switch', 'vi'),
    ]);
})->middleware('setlocale');

// Authentication Routes
Route::middleware(['guest','setlocale'])->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

// Protected Routes - Require Authentication
Route::middleware(['auth','setlocale'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Online Room Show
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');

    // Messaging Routes
    Route::get('/messages', [App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/chats/{user}', [App\Http\Controllers\MessageController::class, 'getMessages'])->name('messages.get');
    Route::post('/messages/chats/{user}', [App\Http\Controllers\MessageController::class, 'sendMessage'])->name('messages.send');
    Route::get('/messages/unread-count', [App\Http\Controllers\MessageController::class, 'getUnreadCount'])->name('messages.unread_count');

    // Group Messaging Routes
    Route::get('/group-messages', [App\Http\Controllers\GroupMessageController::class, 'index'])->name('group_messages.index');
    Route::get('/group-messages/chats/{course}', [App\Http\Controllers\GroupMessageController::class, 'getMessages'])->name('group_messages.get');
    Route::post('/group-messages/chats/{course}', [App\Http\Controllers\GroupMessageController::class, 'sendMessage'])->name('group_messages.send');

    // Student Routes - Chỉ student mới được truy cập
    Route::middleware('checkrole:student')->group(function () {
        Route::get('/student/info', [StudentController::class, 'info'])->name('student.info');
        Route::get('/student/join', [StudentController::class, 'join'])->name('student.join');
        Route::get('/student/classrooms/{classroom}', [StudentController::class, 'showClassroom'])->name('student.classrooms.show');
        Route::get('/student/courses/{course}', [StudentController::class, 'showCourse'])->name('student.courses.show');
        Route::get('/student/assignments/{assignment}/take', [AssignmentController::class, 'take'])
            ->name('student.assignments.take');
        Route::post('/student/assignments/{assignment}/submit', [AssignmentController::class, 'submit'])
            ->name('student.assignments.submit');
        Route::get('/student/assignments/{assignment}/review', [AssignmentController::class, 'review'])
            ->name('student.assignments.review');
        Route::get('/student/quizzes/{quiz}/take', [QuizController::class, 'take'])
            ->name('student.quizzes.take');
        Route::post('/student/quizzes/{quiz}/submit', [QuizController::class, 'submit'])
            ->name('student.quizzes.submit');
        Route::get('/student/quizzes/{quiz}/review', [QuizController::class, 'review'])
            ->name('student.quizzes.review');
        Route::post('/student/classrooms/join', [ClassroomController::class, 'join'])
            ->name('student.classrooms.join');
        Route::post('/student/classrooms/{classroom}/leave', [ClassroomController::class, 'leave'])
            ->name('student.classrooms.leave');
        Route::post('/student/courses/{course}/join', [CourseController::class, 'join'])
            ->name('student.courses.join');
        Route::post('/student/courses/{course}/leave', [CourseController::class, 'leave'])
            ->name('student.courses.leave');
    });

    // Teacher Routes - Chỉ teacher mới được truy cập
    Route::middleware('checkrole:teacher')->group(function () {
        Route::get('/teacher/manage', [TeacherController::class, 'manage'])->name('teacher.manage');
        Route::get('/teacher/manage', [TeacherController::class, 'manageWithSources'])->name('teacher.manage');
        //
        Route::get('/teacher/assignments', [TeacherController::class, 'sources'])->name('teacher.assignments.index');
        Route::get('/teacher/quizzes', [TeacherController::class, 'sources'])->name('teacher.quizzes.index');


        //

        Route::post('/teacher/classrooms', [ClassroomController::class, 'store'])
            ->name('teacher.classrooms.store');
        Route::delete('/teacher/classrooms/{classroom}', [ClassroomController::class, 'destroy'])
            ->name('teacher.classrooms.destroy');

        Route::post('/teacher/classrooms/{classroom}/courses', [CourseController::class, 'store'])
            ->name('teacher.courses.store');
        Route::delete('/teacher/courses/{course}', [CourseController::class, 'destroy'])
            ->name('teacher.courses.destroy');

        Route::post('/teacher/courses/{course}/rooms', [RoomController::class, 'store'])
            ->name('teacher.rooms.store');
        Route::delete('/teacher/rooms/{room}', [RoomController::class, 'destroy'])
            ->name('teacher.rooms.destroy');

        Route::post('/teacher/assignments', [AssignmentController::class, 'storeForTeacher'])
            ->name('teacher.assignments.store');
        Route::get('/teacher/assignments/{assignment}/edit', [AssignmentController::class, 'edit'])
            ->name('teacher.assignments.edit');
        Route::put('/teacher/assignments/{assignment}', [AssignmentController::class, 'update'])
            ->name('teacher.assignments.update');
        Route::delete('/teacher/assignments/{assignment}', [AssignmentController::class, 'destroy'])
            ->name('teacher.assignments.destroy');
        Route::post('/teacher/quizzes', [QuizController::class, 'storeForTeacher'])
            ->name('teacher.quizzes.store');
        Route::get('/teacher/quizzes/{quiz}/edit', [QuizController::class, 'edit'])
            ->name('teacher.quizzes.edit');
        Route::put('/teacher/quizzes/{quiz}', [QuizController::class, 'update'])
            ->name('teacher.quizzes.update');
        Route::delete('/teacher/quizzes/{quiz}', [QuizController::class, 'destroy'])
            ->name('teacher.quizzes.destroy');

        Route::post('/teacher/classrooms/{classroom}/students/{student}/approve', [ClassroomController::class, 'approveStudent'])
            ->name('teacher.students.approve');
        Route::delete('/teacher/classrooms/{classroom}/students/{student}', [ClassroomController::class, 'removeStudent'])
            ->name('teacher.students.remove');
    });
});

// Locale switch (language selector) - accessible to all
Route::get('/locale/{locale}', function ($locale) {
    $allowed = ['en', 'vi'];
    if (in_array($locale, $allowed)) {
        session(['locale' => $locale]);
    }
    $response = redirect()->back();
    if (in_array($locale, $allowed)) {
        $response->cookie('locale', $locale, 60 * 60 * 24 * 365); // 1 year
    }
    return $response;
})->name('locale.switch')->middleware('web');
