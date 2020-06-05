<?php

use Illuminate\Support\Facades\Route;

Route::post('sms/report', [\RolfHaug\FrontSms\Http\Controllers\DeliveryStatusController::class, 'store'])->name('sms.report.store');
