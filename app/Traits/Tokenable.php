<?php

namespace App\Traits;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;

trait Tokenable
{
    /**
     * Get the user session token headers.
     *
     * @return array
     */
    public function userSessionTokenHeaders(): array
    {
        $userSessionToken = $this->getUserSessionToken();

        return array_merge(
            $this->apiTokenHeaders(),
            ['User-Token' => "$userSessionToken"]
        );
    }

    /**
     * Get the api token headers.
     *
     * @return array|string[]
     */
    public function apiTokenHeaders(): array
    {
        $apiToken = config('app.api_token');

        return [
            'Authorization' => "Token token=$apiToken",
        ];
    }

    /**
     * Write the received token to the environment file
     *
     * @param string $token The token to place in the .env file
     *
     * @return void
     */
    public function writeUserSessionTokenToEnvironmentFile(string $token): void
    {
        file_put_contents($this->laravel->environmentFilePath(), preg_replace(
            $this->keyReplacementPattern(),
            'USER_SESSION_TOKEN=' . $token,
            file_get_contents($this->laravel->environmentFilePath())
        ));
    }

    /**
     * Check if the tokens are present.
     *
     * @return bool
     */
    public function checkTokens(): bool
    {
        if (!$this->checkApiToken()) {
            $this->error('Api token not set.');

            return false;
        }

        if (!$this->checkUserSessionToken()) {
            $this->error('User session token not set.');

            return false;
        }

        return true;
    }

    /**
     * Check if thee api token is present.
     *
     * @return bool
     */
    public function checkApiToken(): bool
    {
        return !empty($this->getApiToken());
    }

    /**
     * Check if the user session token is present.
     *
     * @return bool
     */
    public function checkUserSessionToken(): bool
    {
        return !empty($this->getUserSessionToken());
    }


    /**
     * Get a regex pattern that will match env APP_KEY with any random key.
     *
     * @return string
     */
    private function keyReplacementPattern(): string
    {
        $escaped = preg_quote('=' . $this->laravel['config']['app.user_session_token'], '/');

        return "/^USER_SESSION_TOKEN{$escaped}/m";
    }

    /**
     * @return Repository|Application|mixed
     */
    private function getUserSessionToken()
    {
        return config('app.user_session_token');
    }

    /**
     * @return Repository|Application|mixed
     */
    private function getApiToken()
    {
        return config('app.api_token');
    }
}
