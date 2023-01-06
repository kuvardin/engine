<?php

declare(strict_types=1);

namespace App\Exceptions;

use App;
use Kuvardin\FastMysqli\Exceptions\AlreadyExists;
use Kuvardin\FastMysqli\TableRow;
use Throwable;

class LoggedError extends TableRow
{
    private const DB_TABLE = 'errors';

    public const COL_LAST_THROW_DATE = 'last_throw_date';

    readonly public string $hash;
    readonly public string $class;
    readonly public int $code;
    readonly public string $message;
    readonly public string $file;
    readonly public int $line;
    readonly public ?string $trace;

    protected int $throws_number;
    protected int $last_throw_date;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->hash = $data['hash'];
        $this->class = $data['class'];
        $this->code = $data['code'];
        $this->message = $data['message'];
        $this->file = $data['file'];
        $this->line = $data['line'];
        $this->trace = $data['trace'];
        $this->throws_number = $data['throws_number'];
        $this->last_throw_date = $data[self::COL_LAST_THROW_DATE];
    }

    public static function getFilters(
        string $query = null,
    ): array
    {
        $result = [];

        if ($query !== null) {
            $result[] = App::mysqli()->fast_search_exp_gen($query, true, ['class', 'message', 'file', 'trace']);
        }

        return $result;
    }

    public static function genHash(string $class, int $code, string $message, string $file, int $line,
        ?string $trace): string
    {
        return md5($class. ':|:' . $code. ':|:' . $message . ':|:' . $file . ':|:' . $line . ':|:' . $trace);
    }

    public static function makeByHash(string $hash): ?self
    {
        return self::makeByFieldsValues(['hash' => $hash]);
    }

    public static function fix(string $class, int $code, string $message, string $file, int $line, ?string $trace,
        int $throw_date = null): void
    {
        $throw_date ??= time();
        try {
            self::createWithFieldsValues(null, [
                'hash' => self::genHash($class, $code, $message, $file, $line, $trace),
                'class' => $class,
                'code' => $code,
                'message' => $message,
                'file' => $file,
                'line' => $line,
                'trace' => $trace,
                'throws_number' => 1,
                self::COL_LAST_THROW_DATE => $throw_date,
            ], $throw_date);
        } catch (AlreadyExists) {
            $hash = self::genHash($class, $code, $message, $file, $line, $trace,);
            $error = self::makeByHash($hash);
            if ($error !== null) {
                $error->setLastThrowDate($throw_date);
                $error->incThrowsNumber();
                $error->save();
            }
        }
    }

    public static function getDatabaseTableName(): string
    {
        return self::DB_TABLE;
    }

    public static function fixThrowable(Throwable $throwable, int $throw_date = null): void
    {
        self::fix(
            get_class($throwable),
            $throwable->getCode(),
            $throwable->getMessage(),
            $throwable->getFile(),
            $throwable->getLine(),
            $throwable->getTraceAsString(),
            $throw_date,
        );
    }

    public function getThrowsNumber(): int
    {
        return $this->throws_number;
    }

    public function incThrowsNumber(): void
    {
        App::mysqli()->fast_update(self::DB_TABLE, "`throws_number` = (`throws_number` + 1)", ['id' => $this->id], 1);
        $this->throws_number++;
    }

    public function getLastThrowDate(): int
    {
        return $this->last_throw_date;
    }

    public function setLastThrowDate(int $last_throw_date): void
    {
        $this->setFieldValue(self::COL_LAST_THROW_DATE, $this->last_throw_date, $last_throw_date);
    }
}