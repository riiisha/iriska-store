<?php

namespace App\Tests\Controller\Api;

use App\DataFixtures\ProductFixture;
use App\DataFixtures\UserFixture;
use App\Entity\User;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class BaseWebTestCase extends WebTestCase
{

    protected function setUp(): void
    {
        $client = static::createClient();
        $this->client = $client;
        $this->em = $client->getContainer()->get('doctrine')->getManager();

        $this->passwordHasher = $client->getContainer()->get(UserPasswordHasherInterface::class);

        $schemaTool = new SchemaTool($this->em);
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        $loader = new Loader();
        $loader->addFixture(new UserFixture($this->passwordHasher));
        $loader->addFixture(new ProductFixture());
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->execute($loader->getFixtures(), true);
    }

    protected function loginUser(): void
    {
        $user =  $this->em->getRepository(User::class)->findOneBy([
            'email' => 'user@example.com'
        ]);
        $this->client->loginUser($user);
    }

    protected function loginAdmin(): void
    {
        $user =  $this->em->getRepository(User::class)->findOneBy([
            'email' => 'admin@example.com'
        ]);
        $this->client->loginUser($user);
    }
}
