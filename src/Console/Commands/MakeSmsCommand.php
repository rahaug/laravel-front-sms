<?php

namespace RolfHaug\FrontSms\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeSmsCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new a class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../../stubs/DummyClass.php';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return$rootNamespace.'\Notifications\Sms';
    }
}
