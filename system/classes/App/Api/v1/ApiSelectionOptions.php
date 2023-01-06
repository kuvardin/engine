<?php

declare(strict_types=1);

namespace App\Api\v1;

use App;
use App\Api\v1\Input\ApiParameter;
use App\Api\v1\Input\ApiParameterType;
use RuntimeException;

class ApiSelectionOptions
{
    /**
     * @var string Вариант поля сортировки по умолчанию
     */
    protected string $ord_default;

    /**
     * @var ApiSortDirection Направление сортировки по умолчанию
     */
    public ApiSortDirection $sort_default;

    /**
     * @var string[] Все варианты полей сортировки
     */
    protected array $ord_variants = [];

    /**
     * @var int|null Максимальный лимит элементов на страницу
     */
    protected ?int $limit_max;

    public function __construct(
        string $ord_default,
        ApiSortDirection $sort_default,
        array $ord_variants,
        ?int $limit_max = null,
    )
    {
        $this->sort_default = $sort_default;

        foreach ($ord_variants as $ord_variant_alias => $ord_variant) {
            $this->addOrdVariant($ord_variant, is_int($ord_variant_alias) ? $ord_variant : $ord_variant_alias);
        }

        $this->setOrdDefault($ord_default);
        $this->limit_max = $limit_max;
    }

    public static function getApiParameters(): array
    {
        return [
            'page' => ApiParameter::scalar(ApiParameterType::Integer, null, 'Номер страницы'),
            'limit' => ApiParameter::scalar(ApiParameterType::Integer, null, 'Количество элементов на одну страницу'),
            'ord' => ApiParameter::scalar(ApiParameterType::String, null, 'Поле сортировки'),
            'sort' => ApiParameter::scalar(ApiParameterType::String, null, 'Направление сортировки'),
        ];
    }

    public function getOrdDefault(): string
    {
        return $this->ord_default;
    }

    /**
     * Установка поля сортировки по умолчанию
     */
    public function setOrdDefault(string $ord_default): self
    {
        if (!in_array($ord_default, $this->ord_variants, true)) {
            throw new RuntimeException("Unknown ord variant: $ord_default");
        }

        $this->ord_default = $ord_default;
        return $this;
    }

    /**
     * Добавление варианта поля сортировки
     */
    public function addOrdVariant(string $variant, string $alias = null): self
    {
        $this->ord_variants[$alias ?? $variant] = $variant;
        return $this;
    }

    public function getOrdVariant(string $alias): ?string
    {
        return $this->ord_variants[$alias] ?? null;
    }

    /**
     * Все варианты полей сортировки
     *
     * @return string[]
     */
    public function getOrdVariants(): array
    {
        return $this->ord_variants;
    }

    public function setSortDefault(ApiSortDirection $sort_default): self
    {
        $this->sort_default = $sort_default;
        return $this;
    }

    /**
     * Максимальный лимит элементов на страницу
     */
    public function getLimitMax(): ?int
    {
        return $this->limit_max;
    }

    /**
     * Максимальный лимит элементов на страницу отсюда либо из настроек
     */
    public function requireLimitMax(): int
    {
        return $this->limit_max ?? App::settings('items_limit_max.default');
    }

    /**
     * Установка максимального лимита элементов на страницу
     */
    public function setLimitMax(?int $limit_max): self
    {
        if ($limit_max !== null && $limit_max <= 0) {
            throw new RuntimeException("Limit max must be positive number ($limit_max received)");
        }

        $this->limit_max = $limit_max;
        return $this;
    }
}