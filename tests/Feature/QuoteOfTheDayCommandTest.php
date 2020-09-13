<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

it('successfully returns a quote and favorites it', function () {
    $author = 'Veronica A. Shoffstall';
    $quoteId = 1;
    $body = 'Simplicity is the ultimate sophistication.';
    Http::fake([
        config('app.api_url') . '/qotd' => Http::response([
            'quote' => [
                'id' => $quoteId,
                'author' => $author,
                'body' => $body,
            ]
        ]),

        config('app.api_url') . "/quotes/$quoteId/fav" => Http::response([
            'id' => $quoteId,
            'author' => $author,
            'body' => $body,
            'user_details' => [
                'favorite' => true
            ]
        ])
    ]);
    $this->artisan('please')
        ->expectsOutput($body)
        ->expectsOutput("- $author")
        ->expectsQuestion('Do you like this quote?', 'Yes')
        ->expectsOutput("Done!")
        ->assertExitCode(0);
});

it('stops if the user says no', function () {
    $author = 'Veronica A. Shoffstall';
    $quoteId = 1;
    $body = 'Simplicity is the ultimate sophistication.';
    Http::fake([
        config('app.api_url') . '/qotd' => Http::response([
            'quote' => [
                'id' => $quoteId,
                'author' => $author,
                'body' => $body,
            ]
        ])
    ]);
    $this->artisan('please')
        ->expectsOutput($body)
        ->expectsOutput("- $author")
        ->expectsQuestion('Do you like this quote?', '')
        ->assertExitCode(0);
});

it('stops if the api token is not set', function () {
    Config::set('app.api_token', null);

    $author = 'Veronica A. Shoffstall';
    $quoteId = 1;
    $body = 'Simplicity is the ultimate sophistication.';
    Http::fake([
        config('app.api_url') . '/qotd' => Http::response([
            'quote' => [
                'id' => $quoteId,
                'author' => $author,
                'body' => $body,
            ]
        ])
    ]);
    $this->artisan('please')
        ->expectsOutput($body)
        ->expectsOutput("- $author")
        ->expectsQuestion('Do you like this quote?', 'Yes')
        ->expectsOutput('Api token not set.')
        ->assertExitCode(1);
});

it('stops if the session token is not set', function () {
    Config::set('app.user_session_token', null);

    $author = 'Veronica A. Shoffstall';
    $quoteId = 1;
    $body = 'Simplicity is the ultimate sophistication.';
    Http::fake([
        config('app.api_url') . '/qotd' => Http::response([
            'quote' => [
                'id' => $quoteId,
                'author' => $author,
                'body' => $body,
            ]
        ])
    ]);
    $this->artisan('please')
        ->expectsOutput($body)
        ->expectsOutput("- $author")
        ->expectsQuestion('Do you like this quote?', 'Yes')
        ->expectsOutput('User session token not set.')
        ->assertExitCode(1);
});

it('returns quote not found', function () {
    $author = 'Veronica A. Shoffstall';
    $quoteId = 1;
    $body = 'Simplicity is the ultimate sophistication.';
    Http::fake([
        config('app.api_url') . '/qotd' => Http::response([
            'quote' => [
                'id' => $quoteId,
                'author' => $author,
                'body' => $body,
            ]
        ]),

        config('app.api_url') . "/quotes/$quoteId/fav" => Http::response([
            "error_code" => 40,
            "message" => "Quote not found."
        ])
    ]);
    $this->artisan('please')
        ->expectsOutput($body)
        ->expectsOutput("- $author")
        ->expectsQuestion('Do you like this quote?', 'Yes')
        ->expectsOutput('Quote not found.')
        ->assertExitCode(1);
});
it('shows favoriting error', function () {
    $author = 'Veronica A. Shoffstall';
    $quoteId = 1;
    $body = 'Simplicity is the ultimate sophistication.';
    Http::fake([
        config('app.api_url') . '/qotd' => Http::response([
            'quote' => [
                'id' => $quoteId,
                'author' => $author,
                'body' => $body,
            ]
        ]),

        config('app.api_url') . "/quotes/$quoteId/fav" => Http::response([
            'id' => $quoteId,
            'author' => $author,
            'body' => $body,
            'user_details' => [
                'favorite' => false
            ]
        ])
    ]);
    $this->artisan('please')
        ->expectsOutput($body)
        ->expectsOutput("- $author")
        ->expectsQuestion('Do you like this quote?', 'Yes')
        ->expectsOutput('Could not favorite the quote.')
        ->assertExitCode(1);
});
