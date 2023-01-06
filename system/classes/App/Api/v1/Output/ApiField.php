<?php

declare(strict_types=1);

namespace App\Api\v1\Output;

use App\Api\v1\ApiModel;
use RuntimeException;

class ApiField
{
    readonly public ApiFieldType $type;
    readonly public bool $nullable;
    readonly public ?string $description;

    readonly public string|ApiModel|null $model_class;
    readonly public ApiFieldType|null $array_child_type;
    readonly public ApiModel|string|null $array_child_model_class;

    private function __construct(
        ApiFieldType $type,
        bool $nullable,
        ApiModel|string|null $model_class = null,
        ApiFieldType|null $array_child_type = null,
        ApiModel|string|null $array_child_model_class = null,
        string $description = null,
    )
    {
        if ($type === ApiFieldType::Object) {
            if ($model_class === null) {
                throw new RuntimeException("Empty class for field with type {$type->value}");
            }
        }  else {
            if ($model_class !== null) {
                throw new RuntimeException("Not empty class for field with type {$type->value}");
            }
        }

        if ($type === ApiFieldType::Array) {
            if ($array_child_type === ApiFieldType::Object) {
                if ($array_child_model_class === null) {
                    throw new RuntimeException("Empty class for field with type {$array_child_type->value}");
                }
            } else {
                if ($array_child_model_class !== null) {
                    throw new RuntimeException("Not empty class for field with type {$array_child_type->value}");
                }
            }

            if ($array_child_type === ApiFieldType::Array) {
                throw new RuntimeException('Array of array denied');
            }
        }

        if ($model_class !== null) {
            if (!class_exists($model_class)) {
                throw new RuntimeException("API model class $model_class not found");
            }

            if (!is_subclass_of($model_class, ApiModel::class)) {
                throw new RuntimeException("API model class $model_class must be extend for ApiModel");
            }
        }

        if ($nullable && $type === ApiFieldType::Array) {
            throw new RuntimeException('Array cannot be nullable');
        }

        $this->type = $type;
        $this->nullable = $nullable;
        $this->model_class = $model_class;
        $this->description = $description;
        $this->array_child_type = $array_child_type;
        $this->array_child_model_class = $array_child_model_class;
    }

    public static function scalar(
        ApiFieldType $type,
        bool $nullable,
        string $description = null,
    ): self
    {
        if (!$type->isScalar()) {
            throw new RuntimeException("Field type must be scalar, not {$type->name}");
        }

        return new self(
            type: $type,
            nullable: $nullable,
            description: $description,
        );
    }

    public static function object(
        ApiModel|string $model_class,
        bool $nullable,
        string $description = null,
    ): self
    {
        return new self(
            type: ApiFieldType::Object,
            nullable: $nullable,
            model_class: $model_class,
            description: $description,
        );
    }

    public static function array(
        ApiFieldType $child_type,
        ApiModel|string|null $child_model_class = null,
        string $description = null,
    ): self
    {
        return new self(
            type: ApiFieldType::Array,
            nullable: false,
            array_child_type: $child_type,
            array_child_model_class: $child_model_class,
            description: $description,
        );
    }
}