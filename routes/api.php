<?php

use App\Http\Controllers\Api\V1\AdminProjectController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ColorController;
use App\Http\Controllers\Api\V1\ColorFavoriteController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\FavoriteActionController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\FavoriteFolderController;
use App\Http\Controllers\Api\V1\InquiryController;
use App\Http\Controllers\Api\V1\NewsletterSubscriptionController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\ProjectInspirationController;
use App\Http\Controllers\Api\V1\SampleRequestController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\SettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:api')->group(function () {
    Route::prefix('auth')->middleware('throttle:10,1')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('admin/login', [AuthController::class, 'adminLogin']);
        Route::get('me', [AuthController::class, 'me'])->middleware('jwt');
    });

    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
    Route::apiResource('catalogs', CatalogController::class)->only(['index', 'show']);
    Route::get('colors/{color}/texture', [ColorController::class, 'texture'])->name('colors.texture');
    Route::apiResource('colors', ColorController::class)->only(['index', 'show']);
    Route::apiResource('services', ServiceController::class)->only(['index', 'show']);
    Route::post('sample-requests', [SampleRequestController::class, 'store']);
    Route::post('inquiries', [InquiryController::class, 'store']);
    Route::post('newsletter-subscriptions', [NewsletterSubscriptionController::class, 'store']);
    Route::middleware('jwt')->group(function () {
        Route::get('color-favorites', [ColorFavoriteController::class, 'index']);
        Route::patch('colors/{color}/favorite', [ColorFavoriteController::class, 'toggle']);
        Route::patch('projects/{project}/favorite', [ProjectController::class, 'favorite']);
        Route::patch('projects/{project}/archive', [ProjectController::class, 'archive']);
        Route::post('projects/{project}/duplicate', [ProjectController::class, 'duplicate']);
        Route::post('projects/{project}/inspiration', ProjectInspirationController::class);
        Route::apiResource('projects', ProjectController::class);
        Route::apiResource('favorite-folders', FavoriteFolderController::class)->only(['index', 'store', 'destroy']);
        Route::apiResource('favorites', FavoriteController::class);
        Route::post('favorites/{favorite}/projects', [FavoriteActionController::class, 'addToProject']);
        Route::post('favorites/{favorite}/sample-request', [FavoriteActionController::class, 'requestSample']);
    });

    Route::middleware('jwt:admin')->name('admin.')->group(function () {
        Route::get('dashboard', DashboardController::class);
        Route::apiResource('admin/projects', AdminProjectController::class)->only(['index', 'show', 'destroy']);
        Route::apiResource('categories', CategoryController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('catalogs', CatalogController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('colors', ColorController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('services', ServiceController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('inquiries', InquiryController::class)->except('store');
        Route::apiResource('settings', SettingController::class);
        Route::patch('colors/{color}/toggle', [ColorController::class, 'toggle']);
        Route::patch('sample-requests/{sampleRequest}/status', [SampleRequestController::class, 'status']);
        Route::apiResource('sample-requests', SampleRequestController::class)->except('store')->parameters(['sample-requests' => 'sampleRequest']);
        Route::apiResource('newsletter-subscriptions', NewsletterSubscriptionController::class)->except('store')->parameters(['newsletter-subscriptions' => 'newsletterSubscription']);
    });
});
