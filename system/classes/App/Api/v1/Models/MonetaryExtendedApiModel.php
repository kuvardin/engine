<?php

declare(strict_types=1);

namespace App\Api\v1\Models;

use App\Api\v1\ApiModelMutable;
use App\Api\v1\Output\ApiField;
use App\Finance\Currency;
use App\Sessions\Session;

class MonetaryExtendedApiModel extends ApiModelMutable
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
        return 'Расширенное денежное значение';
    }

    public static function getFields(): array
    {
        return [
            'current' => ApiField::object(MonetaryApiModel::class, false, 'Значение "как есть" - в исходной валюте'),
            'adapted' => ApiField::object(MonetaryApiModel::class, true,
                'Адаптированное значение в валюте сессии. Передается только если валюта сессии отличается от исходной'),
        ];
    }

    public function getPublicData(Session $session): array
    {
        return [
            'current' => new MonetaryApiModel($this->value, $this->currency),
            'adapted' => $this->currency->getId() === $session->getCurrencyId()
                ? null
                : new MonetaryApiModel(
                    Currency::convert($this->value, $this->currency, $session->getCurrency()),
                    $session->getCurrency(),
                ),
        ];
    }
}