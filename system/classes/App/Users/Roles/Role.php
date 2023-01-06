<?php

declare(strict_types=1);

namespace App\Users\Roles;

use App;
use App\Actions\CreationTrait;
use App\Actions\DeletableTrait;
use App\Languages\Language;
use App\Languages\Phrase;
use App\Sessions\Session;
use Kuvardin\FastMysqli\Exceptions\AlreadyExists;
use Kuvardin\FastMysqli\Mysqli;
use Kuvardin\FastMysqli\TableRow;

class Role extends TableRow
{
    use DeletableTrait;
    use CreationTrait;

    private const DB_TABLE = 'roles';

    /**
     * @var string[]
     */
    protected array $names;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->initDeletable($data);
        $this->names = Language::getPhraseArray($data, 'name');
        $this->creation_id = $data['creation_id'];
    }

    public static function getDatabaseTableName(): string
    {
        return self::DB_TABLE;
    }

    /**
     * @throws AlreadyExists
     */
    public static function create(Session $session, Phrase $name): self
    {
        $name->throwErrorIfEmpty();
        return self::createCreatableWithFieldsValues(
            $session,
            null,
            $name->getFieldsArray('name', codes: App::settings('languages')),
        );
    }

    public static function getFilters(
        string $query = null,
        bool $deleted = null,
    ): array
    {
        $result = [
            'deletion_date' => Mysqli::get_not_null($deleted),
        ];

        if ($query !== null) {
            $columns = [];
            foreach (App::settings('languages') as $lang_code) {
                $columns[] = "name_$lang_code";
            }

            $result[] = App::mysqli()->fast_search_exp_gen($query, true, $columns);
        }

        return $result;
    }

    public function getName(string $lang_code): ?string
    {
        return $this->names[$lang_code];
    }

    /**
     * @return string[]
     */
    public function getNames(): array
    {
        return $this->names;
    }

    public function setNames(Phrase $name): void
    {
        $name->throwErrorIfEmpty();
        foreach ($name->getArray() as $lang_code => $value) {
            $this->setFieldValue("name_{$lang_code}", $this->names[$lang_code], $value);
        }
    }
}