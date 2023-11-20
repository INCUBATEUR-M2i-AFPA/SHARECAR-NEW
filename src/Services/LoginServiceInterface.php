<?php
namespace App\Services;

use App\Dto\UserDto;

interface LoginServiceInterface
{
    public function loginUser(array $data): array;
}