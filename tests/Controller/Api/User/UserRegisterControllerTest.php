<?php

namespace App\Tests\Controller\Api\User;

use App\DTO\User\UserRegisterDTO;
use App\Tests\Controller\Api\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserRegisterControllerTest extends BaseWebTestCase
{
    protected function getUrl(): string
    {
        return $this->generateUrl('api_user_register');
    }

    protected function getUserRegisterDTO(): UserRegisterDTO
    {
        return new UserRegisterDTO('test', 'test@test.com', '+71111111111', 'test_pass');
    }

    public function testUserRegisterSuccess(): void
    {
        $this->postRequest($this->getUrl(), $this->getUserRegisterDTO());
        $connection = $this->client->getContainer()->get('database_connection');
        $users = $connection->fetchAllAssociative(
            'SELECT * FROM "user" WHERE email = \'' . $this->getUserRegisterDTO()->email . '\''
        );
        $this->assertCount(1, $users);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testUserRegisterNotValidEmail(): void
    {
        $data = $this->getUserRegisterDTO();
        $data->email = 'test@test';

        $this->postRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUserRegisterNotValidPhone(): void
    {
        $data = $this->getUserRegisterDTO();
        $data->phone = '7111111111111111';

        $this->postRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
