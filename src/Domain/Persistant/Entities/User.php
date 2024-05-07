<?php

namespace Fulll\Domain\Persistant\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\OneToMany(targetEntity="Fleet", mappedBy="user", cascade={"persist", "remove"})
     */
    private Collection $fleets;

    public function __construct()
    {
        $this->fleets = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFleets(): Collection
    {
        return $this->fleets;
    }

    public function addFleet(Fleet $fleet): void
    {
        if (!$this->fleets->contains($fleet)) {
            $this->fleets->add($fleet);
            $fleet->setUser($this); // Set the user for the fleet
        }
    }

    public function removeFleet(Fleet $fleet): void
    {
        if ($this->fleets->contains($fleet)) {
            $this->fleets->removeElement($fleet);
            // Optionally, you may unset the user for the fleet here
        }
    }
}


