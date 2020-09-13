<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class QuoteOfTheDayCommandTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testQuoteOfTheDayCommand()
    {
        Http::fake([
            env('API_URL').'/qotd' => Http::response([
                'quote' => [
                    'author' => 'Veronica A. Shoffstall',
                    'body' => 'Simplicity is the ultimate sophistication.',
                ]
            ], 200, ['Headers']),
        ]);

        $this->artisan('qod')
            ->expectsOutput('Simplicity is the ultimate sophistication.')
            ->assertExitCode(0);
    }
}
