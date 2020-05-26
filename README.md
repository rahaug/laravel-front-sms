The package uses the traditional [notification interface](https://laravel.com/docs/7.x/notifications) of Laravel.

An elegant API to send SMS notifications to your users.

## How to use


---

# Setup

**Prerequisites**

Your user model (or auth model) must have a phone field. The field key can be customized in the your env.

## Usage

Predefined messages
```php

$user->notify(new WelcomeSms);

```

You can also send dynamic messages

```php
@TODO: Example of dynamic text
```

Or manual SMS

```php

$message = (new SmsNotification)
->message('This is my message')
->from('Company');

$user->notify($message);
```

## Create new SMS Message

```
php artisan make:notification WelcomeMessage
```

Via FrontMessage


## Config
Add the following to your .env file

```
FRONT_SERVICE_ID=
FRONT_FROM_ID=
FRONT_UNICODE_MESSAGES=true
```



## Incoming SMS Status 

Defaults to /sms/status

Can be overridden by creating your own POST route and use `RolfHaug\FrontSms\Http\Controllers\SmsStatusController@store`