<?php

namespace Tests\Unit;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Artisan;
use RolfHaug\FrontSms\Console\Commands\MakeSmsCommand;
use RolfHaug\FrontSms\Notifications\SmsNotification;
use Tests\TestCase;

class MakeSmsCommandTest extends TestCase
{
    /** @test */
    public function it_registers_the_command()
    {
        $output = Artisan::all();
        $this->assertArrayHasKey('make:sms', $output, 'Command make:sms is not registered');
    }

    /** @test */
    public function it_extends_generator_command()
    {
        /** @var MakeSmsCommand $command */
        $command = resolve(MakeSmsCommand::class);
        $this->assertTrue(is_subclass_of($command, GeneratorCommand::class));
    }

    /** @test */
    public function it_generates_file_under_notifications_sms_namespace()
    {
        $notificationClassName = 'WelcomeMessage';
        $this->artisan('make:sms', ['name' => $notificationClassName]);

        $class = "App\\Notifications\\Sms\\{$notificationClassName}";
        $classPath = app_path("Notifications/Sms/{$notificationClassName}.php");

        $this->assertFileExists($classPath);

        include_once $classPath;

        $this->assertTrue(is_subclass_of(new $class, SmsNotification::class));

        // Cleanup
        unlink($classPath);
    }
}
