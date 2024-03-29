<?php

namespace Fulll\Domain\Memory;

class User
{
    private $id;
    private $fleets;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->fleets = [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function addFleet(Fleet $fleet)
    {
        $this->fleets[] = $fleet;
    }

    // Add methods to retrieve or remove fleets as needed
}