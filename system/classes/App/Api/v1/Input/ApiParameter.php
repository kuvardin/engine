<?php

declare(strict_types=1);

namespace App\Api\v1\Input;

use RuntimeException;

class ApiParameter
{
    readonly public ApiParameterType $type;
    readonly public ?ApiParameterType $child_type;
    readonly public ?int $required_and_empty_error;
    readonly public ?string $description;

    readonly public ?bool $number_positive;
    readonly public ?int $integer_min_value;
    readonly public ?int $integer_max_value;
    readonly public ?int $string_min_length;
    readonly public ?int $string_max_length;
    readonly public ?float $float_min_value;
    readonly public ?float $float_max_value;

    private function __construct(
        ApiParameterType $type,
        ?ApiParameterType $child_type,
        ?int $required_and_empty_error,
        string $description = null,

        bool $number_positive = null,
        int $integer_min_value = null,
        int $integer_max_value = null,
        int $string_min_length = null,
        int $string_max_length = null,
        float $float_min_value = null,
        float $float_max_value = null,
    )
    {
        if ($type === ApiParameterType::Array) {
            if ($child_type === null) {
                throw new RuntimeException('Field with type array must have child type');
            }

            if ($child_type === ApiParameterType::Array) {
                throw new RuntimeException('Array of array are denied');
            }
        } elseif ($child_type !== null) {
            throw new RuntimeException('Field with type scalar must not have child type');
        }

        $this->type = $type;
        $this->child_type = $child_type;
        $this->required_and_empty_error = $required_and_empty_error;
        $this->description = $description;

        $this->number_positive = $number_positive;
        $this->integer_min_value = $integer_min_value;
        $this->integer_max_value = $integer_max_value;
        $this->string_min_length = $string_min_length;
        $this->string_max_length = $string_max_length;
        $this->float_min_value = $float_min_value;
        $this->float_max_value = $float_max_value;
    }


    public static function integer(
        ?int $required_and_empty_error,
        string $description = null,
        ?int $min_value = null,
        ?int $max_value = null,
        ?bool $positive = null,
    ): self
    {
        return new self(
            ApiParameterType::Integer,
            null,
            $required_and_empty_error,
            $description,
            number_positive: $positive,
            integer_min_value: $min_value,
            integer_max_value: $max_value,
        );
    }

    public static function string(
        ?int $required_and_empty_error,
        string $description = null,
        ?int $min_length = null,
        ?int $max_length = null,
    ): self
    {
        return new self(
            ApiParameterType::String,
            null,
            $required_and_empty_error,
            $description,
            string_min_length: $min_length,
            string_max_length: $max_length,
        );
    }

    public static function float(
        ?int $required_and_empty_error,
        string $description = null,
        ?float $positive = null,
        ?float $min_value = null,
        ?float $max_value = null,
    ): self
    {
        return new self(
            ApiParameterType::Float,
            null,
            $required_and_empty_error,
            $description,
            number_positive: $positive,
            float_min_value: $min_value,
            float_max_value: $max_value,
        );
    }

    public static function boolean(
        ?int $required_and_empty_error,
        string $description = null,
    ): self
    {
        return new self(
            ApiParameterType::Boolean,
            null,
            $required_and_empty_error,
            $description,
        );
    }

    public static function scalar(
        ApiParameterType $type,
        ?int $required_and_empty_error,
        string $description = null,
    ): self
    {
        return new self($type, null, $required_and_empty_error, $description);
    }

    public static function array(
        ApiParameterType $child_type,
        ?int $required_and_empty_error,
        string $description = null,
    ): self
    {
        return new self(ApiParameterType::Array, $child_type, $required_and_empty_error, $description);
    }

    public function isRequired(): bool
    {
        return $this->required_and_empty_error !== null;
    }
}