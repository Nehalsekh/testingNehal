<?php

use Illuminate\Support\Facades\Log;

function throwIfDebugging($th, $enableLogging = true)
{
    if ($enableLogging) {
        Log::error($th->getMessage());
    }

    if (config('app.debug') === true) {
        throw $th;
    }
}

function throwIfLocal($th)
{
    if (config('app.env') == 'local') {
        throw $th;
    };
}
