<?php

declare(strict_types=1);

namespace App\Actions;

trait DeletionRequiredTrait
{
    protected int $deletion_id;

    public function getDeletionId(): int
    {
        return $this->deletion_id;
    }

    public function getDeletion(): Action
    {
        return Action::requireById($this->deletion_id);
    }
}