<?php

namespace App\Tests\Controller\Api\Admin;

use App\Entity\User;
use App\Tests\Controller\Api\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminUserEditControllerTest extends BaseWebTestCase
{
    protected function getUrl(): string
    {
        return '/api/admin/user/edit';
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

    public function testEditActionSuccess()
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

    public function testEditActionNotFound()
    {
        $this->loginAdmin();

        $data = $this->getData();
        $data['email'] = 'test_test@example.com';

        $this->putRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testEditActionUnauthorized()
    {
        $data = $this->getData();
        $this->putRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testEditActionForbidden()
    {
        $this->loginUser();
        $data = $this->getData();
        $this->putRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
