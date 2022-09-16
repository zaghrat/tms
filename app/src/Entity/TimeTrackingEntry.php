<?php

namespace App\Entity;

use App\Repository\TimeTrackingEntryRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


#[ORM\Entity(repositoryClass: TimeTrackingEntryRepository::class)]
class TimeTrackingEntry
{
    public const CHECK_IN = 'checking';
    public const CHECK_OUT = 'checkout';
    public const START_PAUSE = 'start-pause';
    public const COMPLETE_PAUSE = 'complete-pause';


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'timeTrackingEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'timeTrackingEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicle $vehicle = null;

    #[ORM\Column(name: "created_at", nullable: false)]
    private DateTimeImmutable $created;


    #[ORM\Column(name: "updated_at", nullable: false)]
    private DateTime $updated;

    #[ORM\Column(name: "typeOfOperation", length: 255, nullable: false)]
    private string $type;

    #[ORM\Column]
    private bool $isCheckedOut = false;

    /**
     * @return DateTimeImmutable
     */
    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    /**
     * @param DateTimeImmutable $created
     * @return TimeTrackingEntry
     */
    public function setCreated(DateTimeImmutable $created): self
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    /**
     * @param DateTime $updated
     */
    public function setUpdated(DateTime $updated): self
    {
        $this->updated = $updated;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): self
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isCheckedOut(): bool
    {
        return $this->isCheckedOut;
    }

    public function setIsCheckedOut(bool $isCheckedOut): self
    {
        $this->isCheckedOut = $isCheckedOut;

        return $this;
    }
}
