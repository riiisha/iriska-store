<?php

namespace App\Tests\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class BaseWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected UserPasswordHasherInterface $passwordHasher;
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        $client = static::createClient();
        $this->client = $client;
        $this->em = $client->getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function loginUser(): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy([
            'email' => 'user@example.com'
        ]);
        $this->client->loginUser($user);
    }

    protected function loginAdmin(): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy([
            'email' => 'admin@example.com'
        ]);
        $this->client->loginUser($user);
    }

    protected function postRequest($url, $data): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
    }

    protected function getRequest($url, $params = []): void
    {
        $this->client->request(Request::METHOD_GET, $url, $params);
    }

    protected function putRequest($url, $data): void
    {
        $this->client->request(
            Request::METHOD_PUT,
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
    }

    protected function patchRequest($url, $data): void
    {
        $this->client->request(
            Request::METHOD_PATCH,
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
    }

    protected function deleteRequest($url, $data): void
    {
        $this->client->request(
            Request::METHOD_DELETE,
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
    }

    abstract protected function getUrl(): string;

    protected function generateUrl(string $routeName): string
    {
        return $this->getContainer()->get('router')->generate($routeName);
    }
}
