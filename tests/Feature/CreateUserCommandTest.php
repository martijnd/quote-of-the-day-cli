<?php

use Illuminate\Support\Facades\Http;

it('returns a user token', function () {
    $userToken = "i4dwXAUNfSUEaTP90K5RWOWLap5cvc9mtjfpvA+c0uufP5GYnpYZUO4k5pTEpwsJHVHNhY5lbLn0cDRpiwsOKA==";
    $username = 'test123';
    Http::fake([
        config('app.api_url') . '/users' => Http::response([
            "User-Token" => $userToken,
            "login" => $username
        ]),
    ]);

    $this->artisan('user:create')
        ->expectsQuestion('Choose a username', $username)
        ->expectsQuestion("What's your your e-mail?", 'mail@mail.com')
        ->expectsQuestion('Choose a password', 'test123pass')
        ->expectsQuestion('Confirm your password', 'test123pass')
        ->expectsOutput('Your user token is ' . $userToken)
        ->assertExitCode(0);
});

it('checks if filled passwords match', function () {
    $userToken = "i4dwXAUNfSUEaTP90K5RWOWLap5cvc9mtjfpvA+c0uufP5GYnpYZUO4k5pTEpwsJHVHNhY5lbLn0cDRpiwsOKA==";
    $username = 'test123';
    Http::fake([
        config('app.api_url') . '/users' => Http::response([
            "User-Token" => $userToken,
            "login" => $username
        ]),
    ]);

    $this->artisan('user:create')
        ->expectsQuestion('Choose a username', $username)
        ->expectsQuestion("What's your your e-mail?", 'mail@mail.com')
        ->expectsQuestion('Choose a password', 'test123pass')
        ->expectsQuestion('Confirm your password', 'doesntmatch')
        ->expectsOutput('Your passwords do not match!')
        // Ask again
        ->expectsQuestion('Choose a password', 'test123pass')
        ->expectsQuestion('Confirm your password', 'test123pass')
        ->expectsOutput('Your user token is ' . $userToken)
        ->assertExitCode(0);
});

it('shows the error response', function () {
    Http::fake([
        config('app.api_url') . '/users' => Http::response([
            "error_code" => 31,
            "message" => "Email is not a valid email; Password is too short (minimum is 5 characters)"
        ]),
    ]);

    $this->artisan('user:create')
        ->expectsQuestion('Choose a username', 'test123')
        ->expectsQuestion("What's your your e-mail?", 'mail@mail.com')
        ->expectsQuestion('Choose a password', 'test123pass')
        ->expectsQuestion('Confirm your password', 'test123pass')
        ->expectsOutput("Email is not a valid email")
        ->expectsOutput("Password is too short (minimum is 5 characters)")
        ->assertExitCode(1);
});
