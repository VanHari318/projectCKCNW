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

// Home page
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

// Protected Routes - Require Authentication
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Student Routes - Chỉ student mới được truy cập
    Route::middleware('checkrole:student')->group(function () {
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
