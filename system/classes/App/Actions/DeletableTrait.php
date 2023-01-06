<?php

declare(strict_types=1);

namespace App\Actions;

use App\Sessions\Session;
use Kuvardin\FastMysqli\TableRow;

/**
 * @mixin TableRow
 */
trait DeletableTrait
{
    use DeletionTrait;

    /**
     * @var int|null Дата удаления
     */
    protected ?int $deletion_date;
    protected bool $table_has_deletion_field;

    public function initDeletable(array $data): void
    {
        $this->table_has_deletion_field = array_key_exists('deletion_id', $data);
        $this->deletion_id = $data['deletion_id'] ?? null;
        $this->deletion_date = $data['deletion_date'];
    }

    public function isDeleted(): bool
    {
        return $this->deletion_date !== null;
    }

    public function getDeletionDate(): ?int
    {
        return $this->deletion_date;
    }

    public function delete(?Session $session, int $deletion_date = null): void
    {
        if ($this->table_has_deletion_field) {
            if ($session !== null) {
                $deletion = Action::create($session, $this, Action::DELETE);
                $this->setFieldValue('deletion_id', $this->deletion_id, $deletion);
            } else {
                $this->setFieldValue('deletion_id', $this->deletion_id, null);
            }
        }

        $this->setFieldValue('deletion_date', $this->deletion_date, $deletion_date ?? time());
        $this->save();
    }

    public function restore(?Session $session): void
    {
        if ($session !== null && $this->table_has_deletion_field) {
            Action::create($session, $this, Action::RESTORE);
        }

        $this->setFieldValue('deletion_date', $this->deletion_date, null);

        if ($this->table_has_deletion_field) {
            $this->setFieldValue('deletion_id', $this->deletion_id, null);
        }

        $this->save();
    }
}