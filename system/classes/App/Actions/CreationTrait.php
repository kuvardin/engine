<?php

declare(strict_types=1);

namespace App\Actions;

trait CreationTrait
{
    protected ?int $creation_id;

    public function getCreationId(): ?int
    {
        return $this->creation_id;
    }

    public function getCreation(): ?Action
    {
        return $this->creation_id === null ? null : Action::requireById($this->creation_id);
    }
}