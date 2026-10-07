<?php

use Amplify\System\Sayt\Http\Controllers\SaytSettingController;
use Amplify\System\Sayt\Http\Controllers\SaytSuggestionController;
use Illuminate\Support\Facades\Route;

Route::get('sayt/search', SaytSuggestionController::class)
    ->middleware('web')
    ->where('keyword', '.{3,}')
    ->name('sayt.search');

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', backpack_middleware(), 'admin_password_reset_required']
], function () {
    Route::crud('sayt-setting', SaytSettingController::class);
});