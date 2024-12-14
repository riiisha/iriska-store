<?php

namespace App\Entity;

use App\Entity\Address\Address;
use App\Entity\Order\Order;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Serializer\Groups(['list'])]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Serializer\Groups(['list'])]
    private string $name;

    #[ORM\Column(length: 180)]
    #[Serializer\Groups(['list'])]
    private string $email;

    #[ORM\Column(length: 15)]
    #[Serializer\Groups(['list'])]
    private string $phone;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Serializer\Groups(['list'])]
    private array $roles;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;

    #[ORM\OneToOne(mappedBy: 'owner', cascade: ['persist', 'remove'])]
    private ?Cart $cart = null;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $orders;

    /**
     * @var Collection<int, Address>
     */
    #[ORM\OneToMany(targetEntity: Address::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $addresses;

    public function __construct(
        string $name,
        string $email,
        string $phone,
        array  $roles = ['ROLE_USER']
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->roles = $roles;
        $this->orders = new ArrayCollection();
        $this->addresses = new ArrayCollection();
    }

    /**
     * @param string $name
     * @param string $phone
     * @param string[] $roles
     * @return void
     */
    public function update(
        string $name,
        string $phone,
        array  $roles = ['ROLE_USER']
    ): void {
        $this->name = $name;
        $this->phone = $phone;
        $this->roles = $roles;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return list<string>
     * @see UserInterface
     *
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }
}
