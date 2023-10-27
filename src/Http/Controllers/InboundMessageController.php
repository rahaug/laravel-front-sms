<?php

namespace RolfHaug\FrontSms\Http\Controllers;

use Illuminate\Routing\Controller;
use RolfHaug\FrontSms\FrontInboundMessage;
use RolfHaug\FrontSms\Http\Requests\StoreInboundSms;

class InboundMessageController extends Controller
{
    public function store(StoreInboundSms $request)
    {
        FrontInboundMessage::createFromRequest($request->validated());

        return response()->json(['received' => true]);
    }
}