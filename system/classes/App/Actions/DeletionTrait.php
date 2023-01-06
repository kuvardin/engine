<?php

declare(strict_types=1);

namespace App\Actions;

trait DeletionTrait
{
    protected ?int $deletion_id;

    public function getDeletionId(): ?int
    {
        return $this->deletion_id;
    }

    public function getDeletion(): ?Action
    {
        return $this->deletion_id === null ? null : Action::requireById($this->deletion_id);
    }
}