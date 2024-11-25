<?php

namespace App\Exception;


use Exception;

class UserExistsException extends Exception
{
    public function __construct()
    {
        parent::__construct('User already exists.');
    }
}