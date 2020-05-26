<?php

use Illuminate\Support\Facades\Route;

Route::post('sms/status', [\RolfHaug\FrontSms\Http\Controllers\FrontMessageStatusController::class, 'store'])->name('sms.status.store');
