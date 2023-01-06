<?php

declare(strict_types=1);

namespace App\Actions;

use App\Sessions\Session;
use Kuvardin\FastMysqli\Exceptions\AlreadyExists;
use Kuvardin\FastMysqli\TableRow;

/**
 * @mixin TableRow
 */
trait CreatableTrait
{
    use CreationTrait;

    /**
     * @throws AlreadyExists
     */
    public static function createCreatableWithFieldsValues(Session $session, ?int $id, array $data,
        int $creation_date = null): self
    {
        $object = self::createWithFieldsValues($id, array_merge($data, ['creation_id' => null]), $creation_date);
        $creation = Action::create($session, $object, Action::CREATE);
        $object->setCreation($creation);
        $object->save();
        return $object;
    }

    protected function setCreation(Action $creation): void
    {
        $this->setFieldValue('creation_id', $this->creation_id, $creation);
    }

    public function initCreation(array $data): void
    {
        $this->creation_id = $data['creation_id'];
    }
}