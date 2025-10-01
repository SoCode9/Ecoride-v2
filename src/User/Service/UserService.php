<?php

namespace App\User\Service;

use App\User\Repository\UserRepository;

use App\Utils\Formatting\OtherFormatter;
final class UserService
{
    public function __construct(private UserRepository $repo) {}

   
}
