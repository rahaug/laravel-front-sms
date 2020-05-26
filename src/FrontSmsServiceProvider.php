<?php

namespace RolfHaug\FrontSms;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class FrontSmsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateSmsStatusesTable')) {
                $this->publishes([
                    __DIR__.'/../database/migrations/create_front_message_statuses_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_sms_statuses_table.php'),
                ], 'migrations');
            }
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/front-sms.php', 'front-sms');

        $this->app->when(FrontClient::class)->needs(Client::class)->give(function () {
            return new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);
        });
    }
}
