<?php

namespace App\Tests\Controller\Api\Admin;

use App\Entity\User;
use App\Tests\Controller\Api\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminUserUpdatePasswordControllerTest extends BaseWebTestCase
{
    protected function getUrl(): string
    {
        return '/api/admin/user/update-password';
    }

    protected function getData(): array
    {
        return [
            'userId' => 2,
            'newPassword' => '1234567890',
        ];
    }

    public function testUpdatePasswordActionSuccess()
    {
        $this->loginAdmin();

        $data = $this->getData();

        $this->patchRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->getById($data['userId']);
        $isPasswordValid = $this->passwordHasher->isPasswordValid($user, $data['newPassword']);
        $this->assertTrue($isPasswordValid);
    }

    public function testEditActionNotFound()
    {
        $this->loginAdmin();

        $data = $this->getData();
        $data['userId'] = 99999;

        $this->patchRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testEditActionUnauthorized()
    {
        $data = $this->getData();
        $this->patchRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testEditActionForbidden()
    {
        $this->loginUser();
        $data = $this->getData();
        $this->patchRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
