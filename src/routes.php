<?php

use Illuminate\Support\Facades\Route;
use RolfHaug\FrontSms\Http\Controllers\DeliveryStatusController;
use RolfHaug\FrontSms\Http\Controllers\InboundMessageController;

Route::post('sms/report', [DeliveryStatusController::class, 'store'])->name('sms.report.store');
Route::post('sms/inbound', [InboundMessageController::class, 'store'])->name('sms.inbound.store');
