<?php

namespace Tests;

use Faker\Factory;
use Illuminate\Support\Collection;
use RolfHaug\FrontSms\FrontSmsServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp() : void
    {
        parent::setUp();
        $this->loadLaravelMigrations(['--database' => 'testing']);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');


        // Add phone to auth model
        include_once __DIR__.'/../database/migrations/add_phone_column_to_user_table.php.stub';
        include_once __DIR__.'/../database/migrations/add_country_code_column_to_user.php.stub';
        (new \AddPhoneColumnToUserTable)->up();
        (new \AddCountryCodeColumnToUser)->up();
    }

    public function getPackageProviders($app)
    {
        return [
            FrontSmsServiceProvider::class
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Bind user model
        $app['config']->set('auth.providers.users.model', User::class);

        // Package config
        $app['config']->set('front-sms.serviceId', '1337');
        $app['config']->set('front-sms.fromId', 'Testsender');

        // import the CreatePostsTable class from the migration
        include_once __DIR__.'/../database/migrations/create_front_messages_table.php.stub';
        include_once __DIR__.'/../database/migrations/create_front_inbound_messages_table.php.stub';
        include_once __DIR__.'/../database/migrations/create_delivery_statuses_table.php.stub';

        // run the up() method of that migration class
        (new \CreateFrontMessagesTable)->up();
        (new \CreateFrontInboundMessagesTable)->up();
        (new \CreateDeliveryStatusesTable)->up();

    }

    /**
     * Create a new user.
     *
     * @param array $overrides
     * @return User
     */
    public function createUser($overrides = [])
    {
        return User::create(array_merge([
            'name' => fake()->name,
            'email' => fake()->email,
            'phone' => fake()->phoneNumber,
            'password' => bcrypt('123456'),
            'country_code' => fake()->countryCode
        ], $overrides));
    }

    public function seed($class = 'DatabaseSeeder')
    {

    }
}
