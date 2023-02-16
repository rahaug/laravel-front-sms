# Laravel Front SMS
Send SMS to your users with Laravel and [Front SMS Gateway API](https://fro.no/sms/sms-gateway?utm_source=Rolf%20Haug%20Laravel%20Package&utm_medium=link&utm_campaign=Package%20Description) through the traditional [notification interface](https://laravel.com/docs/7.x/notifications) of Laravel. 

⚠️ The package support outbound SMS and incoming delivery reports. It does **not** support incoming messages yet. Feel free to contribute.




## Quick usage example

1. Create new SMS message
```php
php artisan make:sms WelcomeMessage
```

Adjust your newly created message.
```php
<?php

namespace App\Notifications\Sms;

use RolfHaug\FrontSms\Notifications\SmsNotification;

class WelcomeMessage extends SmsNotification
{
    public $message = 'Hi %s, today was a good day!';

    public function getMessage($notifiable)
    {
        return vsprintf($this->message, [
            $notifiable->name,
        ]);
    }
}
```

Send it through the notifiable user.

```php
$user = App\User::first();

$user->notify(new \App\Notifications\Sms\WelcomeMessage);
```

---

# Installation

The Laravel Front SMS package can be installed via composer:
```
composer require rolfhaug/laravel-front-sms
```
The package will automatically register a service provider.

You need to publish the config and migration files:

```php
php artisan vendor:publish --provider="RolfHaug\FrontSms\FrontSmsServiceProvider"
```

If you do not have a phone column on the user model already. Publish the user related migrations:

```php
art vendor:publish --provider="RolfHaug\FrontSms\FrontSmsServiceProvider" --tag=user-migrations
```

**Please note:**
If you're serving only one market, you can optionally delete the country_code migration file and rely on the config setting instead. More on this later.

Then run the migrations
```
php artisan migrate
```

If you use Artisan’s `migrate:rollback` or `migrate:refresh` commands you should edit the migration files and uncomment the down methods.


## Configure

Add your configuration in your `.env` file. 
```
# You'll get these fron Front in your settings page
FRONT_SERVICE_ID=
FRONT_SENDER_ID=

# Password if you're not authenticating by IP
FRONT_PASSWORD=

# ISO 3166-2 Geographic Region code (E.g. GB, NO, SE)
FRONT_DEFAULT_REGION=

# Debugging: print messages to the log instead of sending to Front
FRONT_FAKE_MESSAGES=false
```

Register your Delivery report URL in your front settings page
```
yourdomain.com/sms/report
```

Then add the `RolfHaug\FrontSms\Traits\Smsable` trait to your user model.

```php
<?php

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use RolfHaug\FrontSms\Traits\Smsable;

class User extends Authenticatable
{
    use Notifiable, Smsable;

    // ...
}
```

## Required data


**Country Code**

All messages must have a known geographic region (called country code), so we can format the recipients number correctly. This can be set three ways:

1. By specifying an [E.164 formatted](https://en.wikipedia.org/wiki/E.164) phone number. E.g. +4790012345
2. By specifying a `country_code` field on the user model (database column or Eloquent accessor)
3. Setting default region in the config file. E.g. `FRONT_DEFAULT_REGION=NO`

> If you're serving one market, it is recommended to stick to option 3. If you're serving multiple markets, it's recommended to go with option 2.

**Phone field**

 Your user model (or notifiable model) **must have** a phone field. 
 
 The field name for the phone can be customized in the `.env` file. If you for instance want to use a field called `telephone` instead, you can add the following to your `.env` file.
 
 ```
 notifiablePhoneKey=telephone
 ```

## Traits

If you want to send SMS to your User model, you must use the `Notifiable` trait and the `Smsable` trait (see paths in above example).

`Notifiable` is a native Laravel trait. The `Smsable` trait adds two methods `getFormattedPhone()` and `getCountryCode()` that are necessary to successfully send SMS. These methods validate and format the recipients number before sending the SMS.



## Create new messages

The package provides a convenient command to create new messages.

```
php artisan make:sms WelcomeMessage
```

This will create and scaffold a new SMS notification class under the `App/Notifications/Sms` directory.

```php
<?php

namespace App\Notifications\Sms;

use RolfHaug\FrontSms\Notifications\SmsNotification;

class WelcomeMessage extends SmsNotification
{
    public $message = 'Hi %s, today was a good day!';

    public function getMessage($notifiable)
    {
        return vsprintf($this->message, [
            $notifiable->name,
        ]);
    }
}
```

This can be sent through a user that uses the `Notifiable` and `Smsable` traits, as mentioned above.

```php
$user = App\User::first();
$user->notify(new WelcomeMessage);
```

You are free to store your classes in a different directory. It should extend the `RolfHaug\FrontSms\Notifications\SmsNotification` class. 

You can also use the `SmsNotification` class directly.

```php
$message = new SmsNotification('This is my text message.');

// Send it to a user
$user = App\User::first();
$user->notify($message);
```

## Tips

It is recommended to use vsprintf in the `getMessage` function to compile dynamic data from the notifiable (user), like the example above. 

If you want to pass in additional dependencies, it can be done in the constructor.

```php
<?php

namespace App\Notifications\Sms;

use RolfHaug\FrontSms\Notifications\SmsNotification;
use App\Order;

class OrderConfirmation extends SmsNotification
{
    private $order;

    public function __construct($message = null, Order $order)
    {
        $this->order = $order;
        parent::__construct($message);
    }

    public $message = 'Hi %s, thank you for your order! You can download your receipt here %s';

    public function getMessage($notifiable)
    {
        return vsprintf($this->message, [
            $notifiable->name,
            $this->order->receiptLink()
        ]);
    }
}
```


# Success 🎉
You are now ready to send SMS to your users.



## Delivery Reports 

Front will send delivery reports on outbound SMS messages to the url you defined in your Front settings page. By default the route should be set to `yourdomain.com/sms/reports`.

You can override it by creating your own POST route and use `RolfHaug\FrontSms\Http\Controllers\DeliveryStatusController@store`.

```php
Route::post('custom/path/to/sms/report', [\RolfHaug\FrontSms\Http\Controllers\DeliveryStatusController::class, 'store'])->name('sms.report.store');
```


## Testing

The package use the Laravel Notification Interface, so you can test the notifications with [Notification Fake](https://laravel.com/docs/7.x/mocking#notification-fake).

## Debugging

Set `FRONT_FAKE_MESSAGES=true` in your `.env` file to get messages outputted in the Laravel Log. Messages will not be sent to Front with this feature enabled.