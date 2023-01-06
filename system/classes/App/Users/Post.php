<?php

declare(strict_types=1);

namespace App\Users;

use App;
use App\Actions\CreationTrait;
use App\Actions\DeletableTrait;
use App\Languages\Language;
use App\Languages\Phrase;
use App\Projects\ProjectTrait;
use Kuvardin\FastMysqli\TableRow;
use RuntimeException;

class Post extends TableRow
{
    use ProjectTrait;
    use DeletableTrait;
    use CreationTrait;

    private const DB_TABLE = 'users_posts';

    /**
     * @var string[]
     */
    protected array $names;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->project_id = $data['project_id'];
        $this->names = Language::getPhraseArray($data, 'name');
    }

    public static function getDatabaseTableName(): string
    {
        return self::DB_TABLE;
    }

    /**
     * @return string[]
     */
    public function getNames(): array
    {
        return $this->names;
    }

    public function getName(string $lang_code): ?string
    {
        return $this->names[$lang_code];
    }

    public function setNames(Phrase $name): void
    {
        if ($name->isEmpty()) {
            throw new RuntimeException('Empty phrase');
        }

        foreach (App::settings('languages') as $lang_code) {
            $this->setFieldValue("name_$lang_code", $this->names[$lang_code], $name->getValue($lang_code));
        }
    }

    public function can(string $object_class, int $actions, int $allowed_actions = 0): bool
    {
        if ($this->id === 1 && $this->project_id === null) {
            return true;
        }

        return ($actions & $allowed_actions) === $actions;
    }
}