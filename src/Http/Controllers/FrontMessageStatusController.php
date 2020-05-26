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
        FrontMessageStatus::create($request->validated());
        return response()->json('OK');
    }
}
