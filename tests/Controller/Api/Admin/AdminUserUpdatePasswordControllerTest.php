<?php

namespace App\Tests\Controller\Api\Admin;

use App\Entity\User;
use App\Tests\Controller\Api\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserUpdatePasswordControllerTest extends BaseWebTestCase
{
    protected function getUrl(): string
    {
        return $this->generateUrl('api_admin_user_update_password');
    }

    protected function getData(): array
    {
        return [
            'userId' => 2,
            'newPassword' => '1234567890',
        ];
    }

    public function testUpdatePasswordActionSuccess(): void
    {
        $this->loginAdmin();

        $data = $this->getData();

        $this->patchRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $user = $this->em->find(User::class, $data['userId']);
        $this->passwordHasher = $this->client->getContainer()->get(UserPasswordHasherInterface::class);
        $isPasswordValid = $this->passwordHasher->isPasswordValid($user, $data['newPassword']);
        $this->assertTrue($isPasswordValid);
    }

    public function testEditActionNotFound(): void
    {
        $this->loginAdmin();

        $data = $this->getData();
        $data['userId'] = 99999;

        $this->patchRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testEditActionUnauthorized(): void
    {
        $data = $this->getData();
        $this->patchRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testEditActionForbidden(): void
    {
        $this->loginUser();
        $data = $this->getData();
        $this->patchRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
