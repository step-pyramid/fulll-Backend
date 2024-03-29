<?php
namespace Fulll\Infra\Memory;

use Fulll\Domain\Memory\User;

class InMemoryUserRepository
{
    private array $users = [];

    public function save(User $user): void
    {
        $this->users[$user->getId()] = $user;
    }

    public function getById(string $userId): ?User
    {
        return $this->users[$userId] ?? null;
    }

    public function getAllUsers(): array
    {
        return array_values($this->users);
    }
}