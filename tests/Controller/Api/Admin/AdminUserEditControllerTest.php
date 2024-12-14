<?php

namespace App\Tests\Controller\Api\Admin;

use App\Entity\User;
use App\Tests\Controller\Api\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminUserEditControllerTest extends BaseWebTestCase
{
    protected function getUrl(): string
    {
        return $this->generateUrl('api_admin_user_edit');
    }

    protected function getData(): array
    {
        return [
            'email' => 'user@example.com',
            'name' => 'test',
            'phone' => '+79811111111',
            'role' => 'ROLE_USER'
        ];
    }

    public function testEditActionSuccess(): void
    {
        $this->loginAdmin();

        $testName = 'user_test';

        $data = $this->getData();
        $data['name'] = $testName;

        $this->putRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->findByEmail('user@example.com');
        $this->assertEquals($user->getName(), $testName);
    }

    public function testEditActionNotFound(): void
    {
        $this->loginAdmin();

        $data = $this->getData();
        $data['email'] = 'test_test@example.com';

        $this->putRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testEditActionUnauthorized(): void
    {
        $data = $this->getData();
        $this->putRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testEditActionForbidden(): void
    {
        $this->loginUser();
        $data = $this->getData();
        $this->putRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
