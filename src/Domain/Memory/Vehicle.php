<?php
namespace Fulll\Domain\Memory;

class Vehicle
{
    private $plateNumber;
    private $location;

    public function __construct(string $plateNumber)
    {
        $this->plateNumber = $plateNumber;
    }

    public function getPlateNumber(): string
    {
        return $this->plateNumber;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): void
    {
        $this->location = $location;
        $location->setVehicle($this);
    }
}