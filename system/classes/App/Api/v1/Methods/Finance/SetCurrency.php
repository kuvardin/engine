<?php

declare(strict_types=1);

namespace App\Api\v1\Methods\Finance;

use App\Api\v1\ApiMethodMutable;
use App\Api\v1\Exceptions\ApiException;
use App\Api\v1\Input\ApiInput;
use App\Api\v1\Input\ApiParameter;
use App\Api\v1\Input\ApiParameterType;
use App\Api\v1\Models\SessionInfoApiModel;
use App\Api\v1\Output\ApiField;
use App\Finance\Currency;
use App\Sessions\Session;

class SetCurrency extends ApiMethodMutable
{
    public static function getDescription(): ?string
    {
        return 'Установить валюту для текущей сессии';
    }

    public static function getParameters(): array
    {
        return [
            'id' => ApiParameter::scalar(ApiParameterType::Integer, 3010, 'ID валюты'),
        ];
    }

    public static function getResultField(): ?ApiField
    {
        return ApiField::object(SessionInfoApiModel::class, false);
    }

    /**
     * @throws ApiException
     */
    public static function handle(ApiInput $input, Session $session): SessionInfoApiModel
    {
        $id = $input->requireInt('id');
        $currency = Currency::makeById($id);
        $currency !== null ?: throw new ApiException(2007);
        $session->setCurrency($currency);
        $session->save();
        return new SessionInfoApiModel($session);
    }
}