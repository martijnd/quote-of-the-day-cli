<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;

test('quote of the day command works', function () {
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
    $this->artisan('qod')
        ->expectsOutput($body)
        ->expectsOutput("- $author")
        ->expectsQuestion('Do you like this quote?', 'Yes')
        ->assertExitCode(0);
});

test('if quote is not found', function () {
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
    $this->artisan('qod')
        ->expectsOutput($body)
        ->expectsOutput("- $author")
        ->expectsQuestion('Do you like this quote?', 'Yes')
        ->expectsOutput('Quote not found.')
        ->assertExitCode(1);
});
test('if quote is not favorited', function () {
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
    $this->artisan('qod')
        ->expectsOutput($body)
        ->expectsOutput("- $author")
        ->expectsQuestion('Do you like this quote?', 'Yes')
        ->expectsOutput('Could not favorite the quote.')
        ->assertExitCode(1);
});
