<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use http\Encoding\Stream;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\Column(length: 50, nullable: false)]
    private string $id;

    #[ORM\OneToMany(mappedBy: 'Vehicle', targetEntity: TimeTrackingEntry::class)]
    private Collection $timeTrackingEntries;

    public function __construct()
    {
        $this->timeTrackingEntries = new ArrayCollection();
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
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
            $timeTrackingEntry->setVehicle($this);
        }

        return $this;
    }

    public function removeTimeTrackingEntry(TimeTrackingEntry $timeTrackingEntry): self
    {
        if ($this->timeTrackingEntries->removeElement($timeTrackingEntry)) {
            // set the owning side to null (unless already changed)
            if ($timeTrackingEntry->getVehicle() === $this) {
                $timeTrackingEntry->setVehicle(null);
            }
        }

        return $this;
    }
}
