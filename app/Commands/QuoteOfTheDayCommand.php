<?php

namespace App\Commands;

use App\Traits\Tokenable;
use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;

class QuoteOfTheDayCommand extends Command
{
    use Tokenable;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'please';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get the quote of the day';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = Http::get(config('app.api_url') . '/qotd')->json();
        $quoteData = $response['quote'];

        $this->info($quoteData['body']);
        $this->info('- ' . $quoteData['author']);

        if (!$this->handleQuoteFavorite($quoteData['id'])) {
            return 1;
        }

        return 0;
    }

    /**
     * Ask the user's opinion about a quote and let them vote on it.
     *
     * @param int $quoteId
     * @return bool
     */
    private function handleQuoteFavorite(int $quoteId): bool
    {
        // Let the user vote yes or no
        if (!$this->confirm('Do you like this quote?')) {
            return true;
        }

        if (!$this->checkTokens()) {
            return false;
        }

        // Save to api
        $response = Http::withHeaders($this->userSessionTokenHeaders())
            ->put(config('app.api_url') . "/quotes/$quoteId/fav")
            ->json();

        if (isset($response['error_code'])) {
            $this->error($response['message']);

            return false;
        }

        if (isset($response['user_details']) && !$response['user_details']['favorite']) {
            $this->error('Could not favorite the quote.');

            return false;
        }

        $this->line('Done!');

        return true;
    }
}
