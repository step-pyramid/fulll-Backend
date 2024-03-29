<?php
namespace Fulll\Domain\Memory;

class Fleet
{
    private $id;
    private $user; // Add user property
    private $vehicles;

    public function __construct(string $id, User $user)
    {
        $this->id = $id;
        $this->user = $user;
        $this->vehicles = [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    // Add a method to get the user associated with the fleet
    public function getUser(): User
    {
        return $this->user;
    }

    public function addVehicle(Vehicle $vehicle)
    {
        $this->vehicles[] = $vehicle;
    }

    public function getVehicles(): array
    {
        return $this->vehicles;
    }

    public function hasVehicle(Vehicle $vehicle): bool
    {
        foreach ($this->vehicles as $fleetVehicle) {
            if ($fleetVehicle->getPlateNumber() === $vehicle->getPlateNumber()) {
                return true;
            }
        }
        return false;
    }

    public function getVehicleByPlateNumber(string $plateNumber): ?Vehicle
    {
        foreach ($this->vehicles as $vehicle) {
            if ($vehicle->getPlateNumber() === $plateNumber) {
                return $vehicle;
            }
        }
        return null;
    }
}