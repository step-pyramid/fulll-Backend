<?php
namespace Fulll\Infra\Memory;

use Fulll\Domain\Memory\Fleet;

class InMemoryFleetRepository
{
    private array $fleets = [];

    public function save(Fleet $fleet): void
    {
        $this->fleets[$fleet->getId()] = $fleet;
    }

    public function getById(string $fleetId): ?Fleet
    {
        return $this->fleets[$fleetId] ?? null;
    }

    public function getAllFleets(): array
    {
        return array_values($this->fleets);
    }
}
