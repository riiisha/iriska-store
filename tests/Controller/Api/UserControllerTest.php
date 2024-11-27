<?php

namespace App\Tests\Controller\Api;

use Symfony\Component\HttpFoundation\Response;


class UserControllerTest extends BaseWebTestCase
{

    private function getUrl(): string
    {
        return '/api/user/register';
    }

    public function testUserRegisterSuccess()
    {
        $data = [
            'email' => 'test@test.com',
            'phone' => '+71111111111',
            'password' => 'test_pass',
            'name' => 'test',
        ];

        $this->postRequest($this->getUrl(), json_encode($data));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testUserRegisterNotValidEmail()
    {
        $data = [
            'email' => 'test@test',
            'phone' => '+71111111111',
            'password' => 'test_pass',
            'name' => 'test',
        ];

        $this->postRequest($this->getUrl(), json_encode($data));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUserRegisterNotValidPhone()
    {
        $data = [
            'email' => 'test@test.com',
            'phone' => '+7111111111111111',
            'password' => 'test_pass',
            'name' => 'test',
        ];

        $this->postRequest($this->getUrl(), json_encode($data));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
