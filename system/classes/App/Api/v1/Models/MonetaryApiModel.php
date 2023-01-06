<?php

declare(strict_types=1);

namespace App\Api\v1\Models;

use App\Api\v1\ApiModelImmutable;
use App\Api\v1\Output\ApiField;
use App\Api\v1\Output\ApiFieldType;
use App\Finance\Currency;

class MonetaryApiModel extends ApiModelImmutable
{
    protected float $value;
    protected Currency $currency;

    public function __construct(float $value, Currency $currency)
    {
        $this->value = $value;
        $this->currency = $currency;
    }

    public static function getDescription(): ?string
    {
        return 'Денежная величина';
    }

    public static function getFields(): array
    {
        return [
            'value' => ApiField::scalar(ApiFieldType::Float, false, 'Значение'),
            'currency_code' => ApiField::scalar(ApiFieldType::String, false, 'Код валюты'),
            'formatted' => ApiField::scalar(ApiFieldType::String, false, 'Форматированное значение'),
        ];
    }

    public function getPublicData(): array
    {
        return [
            'value' => $this->value,
            'currency_code' => $this->currency->getCodeValue(),
            'formatted' => sprintf(
                $this->currency->getFormat(),
                round($this->value, 2)
            ),
        ];
    }
}