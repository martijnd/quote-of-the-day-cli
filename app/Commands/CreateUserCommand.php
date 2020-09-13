<?php

namespace App\Commands;

use App\Traits\Tokenable;
use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;

class CreateUserCommand extends Command
{
    use Tokenable;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $username = $this->ask('Choose a username');
        $email = $this->ask("What's your your e-mail?");
        $password = $this->secret('Choose a password');
        $passwordConfirm = $this->secret('Confirm your password');

        while ($password !== $passwordConfirm) {
            $this->info('Your passwords do not match!');
            $password = $this->secret('Choose a password');
            $passwordConfirm = $this->secret('Confirm your password');
        }

        $response = Http::withHeaders($this->apiTokenHeaders())
            ->post(config('app.api_url') . '/users', [
                'user' => [
                    'login' => $username,
                    'email' => $email,
                    'password' => $password
                ]
            ])->json();

        if (empty($response['User-Token'])) {
            $errorMessages = collect(explode('; ', $response['message']));
            $errorMessages->each(fn($message) => $this->error($message));

            return 1;
        }
        $userToken = $response['User-Token'];
        $this->info('Your user token is ' . $userToken);
        $this->writeUserSessionTokenToEnvironmentFile($userToken);

        return 0;
    }
}
