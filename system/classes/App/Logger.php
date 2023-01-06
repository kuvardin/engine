<?php

declare(strict_types=1);

namespace App;

use RuntimeException;
use Throwable;

class Logger
{
    /**
     * @param Throwable $exception
     */
    public static function writeException(Throwable $exception): void
    {
        $exception_class = get_class($exception);
        $text = (new DateTime())->format('YmdHisu') . ": $exception_class #{$exception->getCode()}\n" .
            "\tFile: {$exception->getFile()}:{$exception->getLine()}\n" .
            "\tMessage: {$exception->getMessage()}\n" .
            "\tTrace: {$exception->getTraceAsString()}\n\n";
        self::writeToFile(self::getErrorsLogFilePath(), $text);
    }

    public static function writeToFile(string $file_path, string $string, bool $append = true): void
    {
        $f = fopen($file_path, $append ? 'a' : 'w');
        if ($f === false) {
            throw new RuntimeException("File $file_path opening failed");
        }

        if (fwrite($f, $string) === false) {
            throw new RuntimeException("Writing fo file $file_path failed");
        }

        if (!fclose($f)) {
            throw new RuntimeException("File $file_path closing failed");
        }
    }

    public static function getErrorsLogFilePath(): string
    {
        $file_path = LOGS_DIR . '/errors.log';

//        if (!file_exists($file_path)) {
//            $file = fopen($file_path, 'w');
//            fwrite($file, '');
//            fclose($file);
//            chmod($file_path, 0777);
//        }

        return $file_path;
    }

    public static function writeError(string $error): void
    {
        self::writeToFile(self::getErrorsLogFilePath(), $error);
    }
}