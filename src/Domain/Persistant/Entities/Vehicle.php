<?php
namespace Fulll\Domain\Persistant\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="vehicles")
 */
class Vehicle
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", unique=true)
     */
    private string $plateNumber;

    /**
     * @ORM\ManyToMany(targetEntity="Fleet", mappedBy="vehicles")
     */
    private Collection $fleets;

    /**
     * @ORM\OneToOne(targetEntity="Location", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     */
    private ?Location $location;

    public function __construct(string $plateNumber)
    {
        $this->plateNumber = $plateNumber;
        $this->fleets = new ArrayCollection();
        $this->location = null;
    }

    public function getPlateNumber(): string
    {
        return $this->plateNumber;
    }

    public function getFleets(): Collection
    {
        return $this->fleets;
    }

    public function addFleet(Fleet $fleet): void
    {
        if (!$this->fleets->contains($fleet)) {
            $this->fleets->add($fleet);
            $fleet->addVehicle($this); // Add this vehicle to the fleet
        }
    }

    public function removeFleet(Fleet $fleet): void
    {
        if ($this->fleets->contains($fleet)) {
            $this->fleets->removeElement($fleet);
            $fleet->removeVehicle($this); // Remove this vehicle from the fleet
        }
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): void
    {
        $this->location = $location;
        $location->setVehicle($this); // Set this vehicle as the location's associated vehicle
    }

    public function removeLocation(): void
    {
        if ($this->location !== null) {
            $this->location->setVehicle(null); // Remove the association with the current location
            $this->location = null;
        }
    }
}


