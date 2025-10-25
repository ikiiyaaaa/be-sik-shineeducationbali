<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['throttle:6,1'])->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Permissions
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('permissions', PermissionController::class)->parameters(['permissions' => 'id']);
        Route::patch('permissions/{id}/status', [PermissionController::class, 'updateStatus']);
    });

    // Roles
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('roles', RoleController::class)->parameters(['roles' => 'id']);
        Route::patch('roles/{id}/status', [RoleController::class, 'updateStatus']);
    });

    // Users
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('users', UserController::class)->parameters(['users' => 'id']);
        Route::patch('users/{id}/status', [UserController::class, 'updateStatus']);
    });

    // // Teacher Attendances
    // Route::middleware('permission:mengelola teacher attendances')->group(function () {
    //     Route::apiResource('teacher-attendances', TeacherAttendanceController::class);
    //     Route::post('teacher-attendances/check-in', [TeacherAttendanceController::class, 'checkIn']);
    //     Route::post('teacher-attendances/check-out', [TeacherAttendanceController::class, 'checkOut']);
    //     Route::get('teacher-attendances/teacher-stats/{teacherId}', [TeacherAttendanceController::class, 'teacherStats']);
    //     Route::get('teacher-attendances/class-room-stats/{classRoomId}', [TeacherAttendanceController::class, 'classRoomStats']);
    // });

    // // Assets
    // Route::middleware('permission:mengelola assets')->group(function () {
    //     Route::apiResource('assets', AssetController::class);
    //     Route::get('assets/{modelType}/{modelId}', [AssetController::class, 'getAssets']);
    //     Route::post('assets/{modelType}/{modelId}', [AssetController::class, 'uploadAsset']);
    //     Route::post('assets/{modelType}/{modelId}/multiple', [AssetController::class, 'uploadMultipleAssets']);
    // });

    // // Bank Accounts
    // Route::middleware('permission:mengelola bankaccounts')->group(function () {
    //     Route::apiResource('bank-accounts', BankAccountController::class);
    //     Route::patch('bank-accounts/{id}/status', [BankAccountController::class, 'updateStatus']);
    // });
});
