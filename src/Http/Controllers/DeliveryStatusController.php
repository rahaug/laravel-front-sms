<?php

namespace RolfHaug\FrontSms\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use RolfHaug\FrontSms\DeliveryStatus;
use RolfHaug\FrontSms\Http\Requests\StoreDeliveryStatus;

class DeliveryStatusController extends Controller
{
    use ValidatesRequests;

    public function store(StoreDeliveryStatus $request)
    {
        /** @var DeliveryStatus $status */
        $status = DeliveryStatus::create($request->validated());

        if ($status->isReceivedByOperator()) {
            $status->message->markAsReceivedByOperator();
        } elseif ($status->isDelivered()) {
            $status->message->markAsDelivered();
        } elseif ($status->isFailed()) {
            $status->message->markAsFailed();
        }

        return response()->json('OK');
    }
}
