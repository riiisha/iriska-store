<?php

namespace App\Repository\Address;

use App\Entity\Address\Address;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Address>
 */
class AddressRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($registry, Address::class);
    }

    /**
     * @param Address $address
     * @param bool $flash
     * @return void
     */
    public function save(Address $address, bool $flash = false): void
    {
        $this->em->persist($address);
        if ($flash) {
            $this->em->flush();
        }
    }
}
