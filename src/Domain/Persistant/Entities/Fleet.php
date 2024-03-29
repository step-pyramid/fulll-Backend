<?php
namespace Fulll\Domain\Persistant\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity
 * @ORM\Table(name="fleets")
 */
class Fleet
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="fleets")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private User $user;

    /**
     * @ORM\ManyToMany(targetEntity="Vehicle", inversedBy="fleets", cascade={"persist"})
     * @ORM\JoinTable(name="fleet_vehicle",
     *      joinColumns={@ORM\JoinColumn(name="fleet_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="vehicle_plate_number", referencedColumnName="plateNumber")}
     * )
     */
    private Collection $vehicles;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->vehicles = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function addUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function addVehicle(Vehicle $vehicle): void
    {
        $vehicle->addFleet($this); // Add the fleet to the vehicle's collection of fleets
        $this->vehicles[] = $vehicle;
    }

    public function removeVehicle(Vehicle $vehicle): void
    {
        $this->vehicles->removeElement($vehicle);
    }

    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }
}
