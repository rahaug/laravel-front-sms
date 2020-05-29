<?php

namespace RolfHaug\FrontSms\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use RolfHaug\FrontSms\FrontMessageStatus;
use RolfHaug\FrontSms\Http\Requests\StoreFrontMessageStatus;

class FrontMessageStatusController extends Controller
{
    use ValidatesRequests;

    public function store(StoreFrontMessageStatus $request)
    {
        /** @var FrontMessageStatus $status */
        $status = FrontMessageStatus::create($request->validated());

        if ($status->isDelivered()) {
            $status->message->markAsDelivered();
        } elseif ($status->isFailed()) {
            $status->message->markAsFailed();
        }

        return response()->json('OK');
    }
}
