<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevelopmentApplicantController;
use App\Http\Controllers\DevelopmentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventParticipantController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\HeadofFamilyController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SocialAssistanceController;
use App\Http\Controllers\SocialAssistanceRecipientController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('dashboard/head-of-family', [DashboardController::class, 'headOfFamily']);

    Route::get('search', SearchController::class);

    Route::apiResource('user', UserController::class);
    Route::get('user/all/paginated', [UserController::class, 'getAllPaginated']);

    Route::apiResource('head-of-family', HeadofFamilyController::class);
    Route::get('head-of-family/all/paginated', [HeadofFamilyController::class, 'getAllPaginated']);

    Route::apiResource('family-member', FamilyMemberController::class);
    Route::get('family-member/all/paginated', [FamilyMemberController::class, 'getAllPaginated']);

    Route::apiResource('social-assistance', SocialAssistanceController::class);
    Route::get('social-assistance/all/paginated', [SocialAssistanceController::class, 'getAllPaginated']);

    Route::apiResource('social-assistance-recipient', SocialAssistanceRecipientController::class);
    Route::get('social-assistance-recipient/all/paginated', [SocialAssistanceRecipientController::class, 'getAllPaginated']);

    Route::apiResource('event', EventController::class);
    Route::get('event/all/paginated', [EventController::class, 'getAllPaginated']);

    Route::apiResource('event-participant', EventParticipantController::class);
    Route::get('event-participant/all/paginated', [EventParticipantController::class, 'getAllPaginated']);

    Route::apiResource('development', DevelopmentController::class);
    Route::get('development/all/paginated', [DevelopmentController::class, 'getAllPaginated']);

    Route::apiResource('development-applicant', DevelopmentApplicantController::class);
    Route::get('development-applicant/all/paginated', [DevelopmentApplicantController::class, 'getAllPaginated']);

    Route::get('profile', [ProfileController::class, 'index']);
    Route::post('profile', [ProfileController::class, 'store']);
    Route::put('profile', [ProfileController::class, 'update']);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');
});

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::post('/midtrans/callback', [MidtransController::class, 'callback']);
