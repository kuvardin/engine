<?php

declare(strict_types=1);

namespace App\Api\v1\Exceptions;

use App\Languages\Language;
use Exception;
use Throwable;

class ApiException extends Exception
{
    protected static ?array $descriptions = null;

    public const INTERNAL_SERVER_ERROR = 1001;
    public const NOT_ENOUGH_RIGHTS = 2001;

    protected ?string $input_field = null;

    public function __construct(int $code, string $input_field = null, Throwable $previous = null)
    {
        $message = "API exception №$code";
        $this->input_field = $input_field;
        parent::__construct($message, $code, $previous);
    }

    public function getDescriptions(): array
    {
        self::$descriptions ??= require PHRASES_DIR . '/api/v1/api_v1_errors.php';
        return self::$descriptions[$this->code];
    }

    public function getInputField(): ?string
    {
        return $this->input_field;
    }
}