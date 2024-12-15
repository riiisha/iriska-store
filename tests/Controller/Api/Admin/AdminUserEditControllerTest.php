<?php

namespace App\Tests\Controller\Api\Admin;

use App\DTO\User\UserEditDTO;
use App\Entity\User;
use App\Tests\Controller\Api\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminUserEditControllerTest extends BaseWebTestCase
{
    protected function getUrl(): string
    {
        return $this->generateUrl('api_admin_user_edit');
    }

    protected function getUserEditDTO(): UserEditDTO
    {
        return new UserEditDTO('user@example.com', 'test', '+79811111111', 'ROLE_USER');
    }

    public function testEditActionSuccess(): void
    {
        $this->loginAdmin();

        $testName = 'user_test';

        $data = $this->getUserEditDTO();
        $data->name = $testName;

        $this->putRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->findByEmail('user@example.com');
        $this->assertEquals($user->getName(), $testName);
    }

    public function testEditActionNotFound(): void
    {
        $this->loginAdmin();

        $data = $this->getUserEditDTO();
        $data->email = 'test_test@example.com';

        $this->putRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testEditActionUnauthorized(): void
    {
        $data = $this->getUserEditDTO();
        $this->putRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testEditActionForbidden(): void
    {
        $this->loginUser();
        $data = $this->getUserEditDTO();
        $this->putRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
