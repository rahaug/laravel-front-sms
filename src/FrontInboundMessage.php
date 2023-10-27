<?php

namespace RolfHaug\FrontSms;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrontInboundMessage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    protected static function newFactory()
    {
        return FrontMessageFactory::new();
    }

    public static function createFromRequest($request)
    {

        $request = (object) $request;
        $files = null;
        if(isset($request->files) && ! empty($request->files)) {
            $files = $request->files;
        }

        self::create([
            'id' => $request->id,
            'to' => $request->to,
            'from' => $request->from,
            'keyword' => $request->keyword,
            'message' => $request->text,
            'counter' => $request->counter,
            'sent_at' => Carbon::parse($request->sent),
            'files' => $files ?: null
        ]);
    }
}
