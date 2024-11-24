<?php

namespace App\Tests\Controller\Api\Admin;

use App\Entity\User;
use App\Tests\Controller\Api\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;


class AdminUserControllerTest extends BaseWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAdmin();
        $this->endpoint = '/api/admin/user';
    }

    public function testEditActionSuccess()
    {
        $testName = 'user_test';
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $userRepository = $entityManager->getRepository(User::class);

        $data = [
            'email' => 'user@example.com',
            'name' => $testName,
        ];

        $this->client->request(
            'POST', $this->endpoint . '/edit', [], [],
            ['CONTENT_TYPE' => 'application/json'], json_encode($data)
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $user = $userRepository->findOneByEmail('user@example.com');
        $this->assertEquals($user->getName(), $testName);
    }

    public function testEditActionNotFound()
    {
        $data = [
            'email' => 'test_test@example.com',
            'name' => 'test',
        ];

        $this->client->request(
            'POST', $this->endpoint . '/edit', [], [],
            ['CONTENT_TYPE' => 'application/json'], json_encode($data)
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
