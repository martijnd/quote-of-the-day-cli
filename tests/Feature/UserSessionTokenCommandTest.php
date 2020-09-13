<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;

it('sets the user session token', function () {
    $token = 'testToken';
    Http::fake([
        config('app.api_url') . '/session' => Http::response([
            'User-Token' => $token
        ])
    ]);
    $this->artisan('user:token')
        ->expectsQuestion('What is your username?', 'test123')
        ->expectsQuestion('What is your password?', 'test123')
        ->expectsOutput("Your user token is $token")
        ->assertExitCode(0);
});

it('displays the user session token error messages', function () {
    Http::fake([
        config('app.api_url') . '/session' => Http::response([
            'error_code' => 30,
            'message' => 'Session not found.'
        ])
    ]);
    $this->artisan('user:token')
        ->expectsQuestion('What is your username?', 'test123')
        ->expectsQuestion('What is your password?', 'test123')
        ->expectsOutput('Session not found.')
        ->assertExitCode(1);
});
