<?php

namespace App\Services;

interface RegisterServiceInterface
{
    public function register(array $data): array;

    public function confirmRegister(string $token): array;

    public function forgotPassword(string $email): array;

    public function resetPassword(string $token, string $password): array;

}
