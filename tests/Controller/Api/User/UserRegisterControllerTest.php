<?php

namespace App\Tests\Controller\Api\User;

use App\Tests\Controller\Api\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserRegisterControllerTest extends BaseWebTestCase
{
    private function getUrl(): string
    {
        return '/api/user/register';
    }

    protected function getData(): array
    {
        return [
            'email' => 'test@test.com',
            'phone' => '+71111111111',
            'password' => 'test_pass',
            'name' => 'test',
        ];
    }

    public function testUserRegisterSuccess()
    {
        $this->postRequest($this->getUrl(), $this->getData());

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testUserRegisterNotValidEmail()
    {
        $data = $this->getData();
        $data['email'] = 'test@test';

        $this->postRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUserRegisterNotValidPhone()
    {
        $data = $this->getData();
        $data['phone'] = '7111111111111111';

        $this->postRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
