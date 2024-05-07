<?php
namespace Fulll\Domain\Memory;

class Location
{
    private float $latitude;
    private float $longitude;
    private ?float $altitude;
    private $vehicle; 

    public function __construct(float $latitude, float $longitude, ?float $altitude = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->altitude = $altitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getAltitude(): ?float
    {
        return $this->altitude;
    }

    public function isEqual(Location $location): bool
    {
        return $this->latitude === $location->getLatitude() &&
               $this->longitude === $location->getLongitude() &&
               $this->altitude === $location->getAltitude();
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle;
    }
}