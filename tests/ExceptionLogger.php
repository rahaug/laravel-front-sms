<?php

namespace Tests;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;

class ExceptionLogger implements ExceptionHandler
{
    public function report(Exception $e)
    {
    }

    public function shouldReport(Exception $e)
    {
        // TODO: Implement shouldReport() method.
    }

    public function render($request, Exception $e)
    {
        // TODO: Implement render() method.

        dump($e->getMessage());
        dump(get_class($e));
    }

    public function renderForConsole($output, Exception $e)
    {
        // TODO: Implement renderForConsole() method.
    }
}
