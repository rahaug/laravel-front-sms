<?php

namespace Tests;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use RolfHaug\FrontSms\Traits\Smsable;

class User extends Authenticatable
{
    use Notifiable, Smsable;

    protected $guarded = [];
}
