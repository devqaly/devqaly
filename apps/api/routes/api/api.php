<?php

use Illuminate\Support\Facades\Route;

require 'auth/auth.php';
require 'auth/registerToken.php';
require 'resources/sessions.php';
require 'resources/user.php';
require 'resources/company.php';
require 'resources/project.php';
require 'resources/events.php';

Route::get('test', function () {
    return response()->json(['status' => 'okay']);
});

if (config('app.env') === 'local') {
    require 'cypress.php';
}
