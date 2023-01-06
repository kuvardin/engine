<?php

declare(strict_types=1);

namespace App\Api\v1\Models;

use App\Api\v1\ApiModelImmutable;
use App\Api\v1\Output\ApiField;
use App\Api\v1\Output\ApiFieldType;
use App\Finance\Currency;

class CurrencyApiModel extends ApiModelImmutable
{
    protected Currency $currency;

    public function __construct(Currency $currency)
    {
        $this->currency = $currency;
    }

    public static function getDescription(): ?string
    {
        return 'Информация о валюте';
    }

    public static function getCacheTtl(): int
    {
        return 3 * 3600;
    }

    public static function getFields(): array
    {
        return [
            'id' => ApiField::scalar(ApiFieldType::Integer, false, 'ID валюты'),
            'code' => ApiField::scalar(ApiFieldType::String, false, '3-значный код'),
            'symbol' => ApiField::scalar(ApiFieldType::String, false, 'Символ'),
            'format' => ApiField::scalar(ApiFieldType::String, false, 'Формат вставки символа'),
            'price_hundred_dollars' => ApiField::scalar(ApiFieldType::Float, false, 'Стоимость 100 долларов'),
        ];
    }

    public function getPublicData(): array
    {
        return [
            'id' => $this->currency->getId(),
            'code' => $this->currency->getCodeValue(),
            'symbol' => $this->currency->getSymbol(),
            'format' => $this->currency->getFormat(),
            'price_hundred_dollars' => $this->currency->getPriceHundredDollars(),
        ];
    }
}