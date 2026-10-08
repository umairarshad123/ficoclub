<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Run $work after the response has been sent to the client (PHP-FPM /
     * LiteSpeed flush the response first). Laravel never clears terminating
     * callbacks, so guard against a long-lived app instance re-running them.
     */
    protected function afterResponse(callable $work): void
    {
        $ran = false;

        app()->terminating(function () use (&$ran, $work) {
            if ($ran) {
                return;
            }
            $ran = true;
            $work();
        });
    }
}
