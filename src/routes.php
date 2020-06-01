<?php

use Illuminate\Support\Facades\Route;

Route::post('sms/status', [\RolfHaug\FrontSms\Http\Controllers\DeliveryStatusController::class, 'store'])->name('sms.status.store');
