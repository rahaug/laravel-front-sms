<?php

namespace Tests;

use Faker\Factory;
use Illuminate\Contracts\Debug\ExceptionHandler;
use RolfHaug\FrontSms\FrontSmsServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp() : void
    {
        parent::setUp();
        $this->loadLaravelMigrations(['--database' => 'testing']);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->withFactories(__DIR__.'/../database/factories');

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
        $app['config']->set('app.debug', true);
        $app['config']->set('auth.providers.users.model', User::class);
        $app->bind(ExceptionHandler::class, ExceptionLogger::class);

        // import the CreatePostsTable class from the migration
        include_once __DIR__.'/../database/migrations/create_front_messages_table.php.stub';
        include_once __DIR__.'/../database/migrations/create_front_message_statuses_table.php.stub';

        // run the up() method of that migration class
        (new \CreateFrontMessagesTable)->up();
        (new \CreateFrontMessageStatusesTable)->up();
    }

    /**
     * Create a new user.
     *
     * @param array $overrides
     * @return User
     */
    public function createUser($overrides = [])
    {
        $faker = Factory::create();

        return User::create(array_merge([
            'name' => $faker->name,
            'email' => $faker->email,
            'phone' => $faker->phoneNumber,
            'password' => bcrypt('123456'),
            'country_code' => $faker->countryCode
        ], $overrides));
    }
}
