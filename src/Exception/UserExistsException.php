<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserExistsException extends HttpException
{
    public function __construct()
    {
        parent::__construct(Response::HTTP_CONFLICT, 'User already exists.');
    }
}
