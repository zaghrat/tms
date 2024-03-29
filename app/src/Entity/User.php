<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_DRIVER = 'ROLE_DRIVER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;

    #[ORM\Column(length: 50)]
    private string $firstname;

    #[ORM\Column(length: 50)]
    private string $lastname;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TimeTrackingEntry::class, orphanRemoval: true)]
    private Collection $timeTrackingEntries;

    #[ORM\Column(nullable: false)]
    private DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->timeTrackingEntries = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_DRIVER;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return ucfirst($this->firstname);
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = ucfirst($lastname);

        return $this;
    }

    /**
     * @return Collection<int, TimeTrackingEntry>
     */
    public function getTimeTrackingEntries(): Collection
    {
        return $this->timeTrackingEntries;
    }

    public function addTimeTrackingEntry(TimeTrackingEntry $timeTrackingEntry): self
    {
        if (!$this->timeTrackingEntries->contains($timeTrackingEntry)) {
            $this->timeTrackingEntries->add($timeTrackingEntry);
            $timeTrackingEntry->setUser($this);
        }

        return $this;
    }

    public function removeTimeTrackingEntry(TimeTrackingEntry $timeTrackingEntry): self
    {
        if ($this->timeTrackingEntries->removeElement($timeTrackingEntry)) {
            // set the owning side to null (unless already changed)
            if ($timeTrackingEntry->getUser() === $this) {
                $timeTrackingEntry->setUser(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
