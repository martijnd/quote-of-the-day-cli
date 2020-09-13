<?php


namespace App\Commands;


use App\Traits\Tokenable;
use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;

class UserSessionTokenCommand extends Command
{
    use Tokenable;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'user:token';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get the user token';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $username = $this->ask('What is your username?');
        $password = $this->secret('What is your password?');

        $response = Http::withHeaders($this->apiTokenHeaders())
            ->post(config('app.api_url') . '/session', [
                'user' => [
                    'login' => $username,
                    'password' => $password
                ]
            ]);

        if (!isset($response->json()['User-Token'])) {
            $this->error($response->json()['message']);

            return 1;
        }

        $userToken = $response->json()['User-Token'];
        $this->info("Your user token is $userToken");
        $this->writeUserSessionTokenToEnvironmentFile($userToken);

        return 0;
    }
}
