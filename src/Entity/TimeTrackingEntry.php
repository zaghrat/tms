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
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'timeTrackingEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'timeTrackingEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicle $Vehicle = null;

    /**
     * @var DateTimeImmutable $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetimetz_immutable")
     */
    private DateTimeImmutable $created;

    /**
     * @var DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetimetz")
     */
    private DateTime $updated;

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
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
    public function setUpdated(DateTime $updated): void
    {
        $this->updated = $updated;
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
        return $this->Vehicle;
    }

    public function setVehicle(?Vehicle $Vehicle): self
    {
        $this->Vehicle = $Vehicle;

        return $this;
    }
}
