<?php

namespace RolfHaug\FrontSms;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use RolfHaug\FrontSms\Console\Commands\MakeSmsCommand;

class FrontSmsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->mergeConfigFrom(__DIR__.'/../config/front-sms.php', 'front-sms');

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeSmsCommand::class
            ]);

            $this->publishes([
                __DIR__.'/../config/front-sms.php' => config_path('front-sms.php')
            ], 'config');

            $this->publishes([
                __DIR__.'/../database/migrations/create_front_messages_table.php.stub' => database_path('migrations/'.date('Y_m_d_His_', time()).'create_front_messages_table.php'),
                __DIR__.'/../database/migrations/create_front_inbound_messages_table.php.stub' => database_path('migrations/'.date('Y_m_d_His_', time()).'create_front_inbound_messages_table.php'),
                __DIR__.'/../database/migrations/create_delivery_statuses_table.php.stub' => database_path('migrations/'.date('Y_m_d_His_', time()).'create_delivery_statuses_table.php'),
            ], 'migrations');

            $this->publishes([
                __DIR__.'/../database/migrations/add_phone_column_to_user_table.php.stub' => database_path('migrations/'.date('Y_m_d_His_', time()).'add_phone_column_to_user_table.php'),
                __DIR__.'/../database/migrations/add_country_code_column_to_user.php.stub' => database_path('migrations/'.date('Y_m_d_His_', time()).'add_country_code_column_to_user.php'),
            ], 'user-migrations');
        }
    }

    public function register()
    {
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
