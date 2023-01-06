<?php

declare(strict_types=1);

namespace App\Actions;

trait CreationRequiredTrait
{
    protected int $creation_id;

    public function getCreationId(): int
    {
        return $this->creation_id;
    }

    public function getCreation(): Action
    {
        return Action::requireById($this->creation_id);
    }
}