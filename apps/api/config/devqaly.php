<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Is self-hosted deployment
    |--------------------------------------------------------------------------
    |
    | Here you may specify if this deployed version of devqaly is self-hosted or not.
    | We will use this value to check for a couple of things such as:
    | 1. Should we care about subscriptions? Self-hosted instances do not need to have limits imposed
    | 2. More to come...
    |
    */

    'isSelfHosting' => env('DEVQALY_IS_SELF_HOSTING', true),
];
