<?php

declare(strict_types=1);

namespace App\Api\v1\Models;

use App\Api\v1\ApiModelImmutable;
use App\Api\v1\ApiSelectionOptions;
use App\Api\v1\Output\ApiField;
use App\Api\v1\Output\ApiFieldType;
use Kuvardin\FastMysqli\SelectionData;

class SelectionDataApiModel extends ApiModelImmutable
{
    protected SelectionData $selection_data;
    protected ApiSelectionOptions $selection_options;

    public function __construct(SelectionData $selection_data, ApiSelectionOptions $selection_options)
    {
        $this->selection_data = $selection_data;
        $this->selection_options = $selection_options;
    }

    public static function getDescription(): ?string
    {
        return 'Данные о выборке элементов';
    }

    public static function getFields(): array
    {
        return [
            'page' => ApiField::scalar(ApiFieldType::Integer, false, 'Номер страницы'),
            'limit' => ApiField::scalar(ApiFieldType::Integer, false, 'Максимальное количество элементов на страницу'),
            'ord' => ApiField::scalar(ApiFieldType::String, true, 'Поле, по которому произведена сортировка'),
            'sort' => ApiField::scalar(ApiFieldType::String, true, 'Направление сортировки (ASC/DESC)'),
            'total' => ApiField::scalar(ApiFieldType::Integer, true, 'Общее количество элементов'),
        ];
    }

    public function getPublicData(): array
    {
        return [
            'page' => $this->selection_data->getLimit() !== null && $this->selection_data->getOffset() !== null
                ? $this->selection_data->getPage()
                : 1,
            'limit' => $this->selection_data->getLimit(),
            'ord' => $this->selection_data->getOrd(),
            'sort' => $this->selection_data->getSort(),
            'total' => $this->selection_data->total_amount,
        ];
    }
}