<?php
namespace Fulll\Infra\Memory;

use Fulll\Domain\Memory\Vehicle;

class InMemoryVehicleRepository
{
    private $vehicles = [];

    public function save(Vehicle $vehicle): void
    {
        $this->vehicles[$vehicle->getPlateNumber()] = $vehicle;
    }

    public function getByPlateNumber(string $plateNumber): ?Vehicle
    {
        return $this->vehicles[$plateNumber] ?? null;
    }

    public function getAllVehicles(): array
    {
        return $this->vehicles;
    }
}
