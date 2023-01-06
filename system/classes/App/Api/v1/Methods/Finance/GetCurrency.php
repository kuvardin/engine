<?php

declare(strict_types=1);

namespace App\Api\v1\Methods\Finance;

use App\Api\v1\ApiMethodImmutable;
use App\Api\v1\Exceptions\ApiException;
use App\Api\v1\Input\ApiInput;
use App\Api\v1\Input\ApiParameter;
use App\Api\v1\Input\ApiParameterType;
use App\Api\v1\Models\CurrencyApiModel;
use App\Api\v1\Output\ApiField;
use App\Finance\Currency;

class GetCurrency extends ApiMethodImmutable
{
    public static function getDescription(): ?string
    {
        return 'Получить информацию о валюте';
    }

    public static function getParameters(): array
    {
        return [
            'id' => ApiParameter::scalar(ApiParameterType::Integer, 3010, 'ID валюты'),
        ];
    }

    public static function getResultField(): ?ApiField
    {
        return ApiField::object(CurrencyApiModel::class, false);
    }

    public static function handle(ApiInput $input): CurrencyApiModel
    {
        $id = $input->requireInt('id');
        $currency = Currency::makeById($id);
        $currency !== null ?: throw new ApiException(2007);
        return new CurrencyApiModel($currency);
    }
}