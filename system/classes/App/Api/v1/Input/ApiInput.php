<?php

declare(strict_types=1);

namespace App\Api\v1\Input;

use App;
use App\Api\v1\ApiSelectionOptions;
use App\Api\v1\ApiSortDirection;
use App\Api\v1\Exceptions\ApiException;
use App\Languages\Language;
use App\Languages\Phrase;
use GuzzleHttp\Psr7\UploadedFile;
use JsonException;
use Kuvardin\DataFilter\DataFilter;
use Kuvardin\FastMysqli\SelectionData;
use RuntimeException;

class ApiInput
{
    /**
     * @var ApiParameter[]
     */
    protected array $parameters;

    protected array $data = [];

    /**
     * @var UploadedFile[]
     */
    protected array $files = [];

    public readonly ?ApiSelectionOptions $selection_options;

    protected ?ApiException $exception = null;

    /**
     * @param ApiParameter[] $parameters
     * @throws ApiException
     */
    public function __construct(
        array $parameters,
        array $input_data,
        array $files,
        string $language_code,
        ?ApiSelectionOptions $selection_options,
    )
    {
        $this->parameters = $parameters;

        $this->selection_options = $selection_options;

        $fields_with_errors = [];

        if ($input_data !== []) {
            foreach ($input_data as $input_data_key => $input_data_value) {
                if ($input_data_value === '') {
                    $input_data_value = null;
                }

                if (isset($this->parameters[$input_data_key])) {
                    switch ($this->parameters[$input_data_key]->type) {
                        case ApiParameterType::Integer:
                            if (is_int($input_data_value)) {
                                $this->data[$input_data_key] = $input_data_value;
                            } elseif (is_string($input_data_value)) {
                                if ((string)(int)$input_data_value === $input_data_value) {
                                    $this->data[$input_data_key] = (int)$input_data_value;
                                }
                            }

                            if (!isset($this->data[$input_data_key])) {
                                $fields_with_errors[] = $input_data_key;
                                $this->addException($input_data_key, 3011);
                            }
                            break;

                        case ApiParameterType::Boolean:
                            if (is_bool($input_data_value)) {
                                $this->data[$input_data_key] = $input_data_value;
                            } elseif (is_string($input_data_value)) {
                                if ($input_data_value === '0' || $input_data_value === '1') {
                                    $this->data[$input_data_key] = $input_data_value === '1';
                                } else {
                                    $input_data_value_lowercase = strtolower($input_data_value);
                                    if ($input_data_value_lowercase === 'true' ||
                                        $input_data_value_lowercase === 'false') {
                                        $this->data[$input_data_key] = $input_data_value_lowercase === 'true';
                                    }
                                }
                            } elseif ($input_data_value === 0 || $input_data_value === 1) {
                                $this->data[$input_data_key] = $input_data_value === 1;
                            }
                            break;



                        case ApiParameterType::String:
                            if (is_string($input_data_value)) {
                                $input_data_value = DataFilter::getStringEmptyToNull($input_data_value, true);
                                if ($input_data_value !== null) {
                                    $this->data[$input_data_key] = $input_data_value;
                                }
                            } elseif (is_int($input_data_value) || is_float($input_data_value)) {
                                $this->data[$input_data_key] = (string)$input_data_value;
                            }
                            break;

                        case ApiParameterType::Float:
                            if (is_float($input_data_value)) {
                                $this->data[$input_data_key] = $input_data_value;
                            } elseif (is_int($input_data_value)) {
                                $this->data[$input_data_key] = (float)$input_data_value;
                            } elseif (is_string($input_data_value) && is_numeric($input_data_value)) {
                                $this->data[$input_data_key] = (float)$input_data_value;
                            }
                            break;

                        case ApiParameterType::Phrase:
                            if (is_array($input_data_value)) {
                                $phrase = new Phrase;
                                foreach ($input_data_value as $phrase_key => $phrase_value) {
                                    if (is_string($phrase_key) && Language::checkLangCode($phrase_key) &&
                                        is_string($phrase_value)) {
                                        $phrase_value = DataFilter::getStringEmptyToNull($phrase_value, true);
                                        if ($phrase_value !== null) {
                                            $phrase->setValue($phrase_key, $phrase_value);
                                        }
                                    }
                                }

                                if (!$phrase->isEmpty()) {
                                    $this->data[$input_data_key] = $phrase;
                                }
                            } elseif (is_string($input_data_value)) {
                                $input_data_value = DataFilter::getStringEmptyToNull($input_data_value, true);
                                if ($input_data_value !== null) {
                                    $this->data[$input_data_key] = Phrase::make($language_code, $input_data_value);
                                }
                            }
                            break;

                        case ApiParameterType::Array:
                            switch ($this->parameters[$input_data_key]->child_type) {
                                case ApiParameterType::Integer:
                                    if (is_int($input_data_value)) {
                                        $this->data[$input_data_key] = [$input_data_value];
                                    } elseif (is_string($input_data_value) || is_array($input_data_value)) {
                                        $result = [];
                                        $input_data_value_parts = is_string($input_data_value)
                                            ? explode(',', $input_data_value)
                                            : $input_data_value;

                                        foreach ($input_data_value_parts as $input_data_value_part) {
                                            $input_data_value_part = trim($input_data_value_part);
                                            if ((string)(int)$input_data_value_part === $input_data_value_part) {
                                                $result[] = (int)$input_data_value_part;
                                            } else {
                                                break 3;
                                            }
                                        }

                                        $this->data[$input_data_key] = $result === [] ? null : $result;
                                    }
                                    break;

                            }
                    }
                }
            }
        }

        ksort($this->data);

        foreach ($this->parameters as $parameter_name => $parameter) {
            if ($parameter->required_and_empty_error !== null && !isset($this->data[$parameter_name])) {
                if (!in_array($parameter_name, $fields_with_errors, true)) {
                    $this->addException($parameter_name, $parameter->required_and_empty_error);
                }
            }
        }
    }

    public function getInt(string $name): ?int
    {
        return $this->getScalar($name, ApiParameterType::Integer);
    }

    public function requireInt(string $name): int
    {
        return $this->getScalar($name, ApiParameterType::Integer, true);
    }

    public function getString(string $name): ?string
    {
        return $this->getScalar($name, ApiParameterType::String);
    }

    public function requireString(string $name): string
    {
        return $this->getScalar($name, ApiParameterType::String, true);
    }

    public function getBool(string $name): ?bool
    {
        return $this->getScalar($name, ApiParameterType::Boolean);
    }

    public function requireBool(string $name): bool
    {
        return $this->getScalar($name, ApiParameterType::Boolean, true);
    }

    public function getPhrase(string $name): ?Phrase
    {
        return $this->getScalar($name, ApiParameterType::Phrase);
    }

    public function requirePhrase(string $name): Phrase
    {
        return $this->getScalar($name, ApiParameterType::Phrase, true);
    }

    protected function getScalar(string $name, ApiParameterType $type, bool $require = false): mixed
    {
        if (!array_key_exists($name, $this->parameters)) {
            throw new RuntimeException("Parameter with name $name not found");
        }

        if ($this->parameters[$name]->type !== $type) {
            throw new RuntimeException(
                "Unable get parameter \"$name\" with type {$type->value} " .
                "(type must be {$this->parameters[$name]->type->value})"
            );
        }

        if ($require && !$this->parameters[$name]->isRequired()) {
            throw new RuntimeException("Unable require parameter \"$name\"");
        }

        return $this->data[$name] ?? null;
    }

    protected function getArray(string $name, ApiParameterType $child_type, bool $require = false): mixed
    {
        if (!array_key_exists($name, $this->parameters)) {
            throw new RuntimeException("Parameter with name $name not found");
        }

        if ($this->parameters[$name]->type !== ApiParameterType::Array) {
            throw new RuntimeException(
                "Unable get parameter \"$name\" with type Array " .
                "(type must be {$this->parameters[$name]->type->value})"
            );
        }

        if ($this->parameters[$name]->child_type !== $child_type) {
            throw new RuntimeException(
                "Unable get parameter \"$name\" with child type {$child_type->value} " .
                "(type must be {$this->parameters[$name]->child_type->value})"
            );
        }

        if ($require && !$this->parameters[$name]->isRequired()) {
            throw new RuntimeException("Unable require parameter \"$name\"");
        }

        return $this->data[$name] ?? null;
    }

    /**
     * @param string $name
     * @return int[]|null
     */
    public function getArrayOfInt(string $name): ?array
    {
        return $this->getArray($name, ApiParameterType::Integer);
    }

    /**
     * @param string $name
     * @return int[]
     */
    public function requireArrayOfInt(string $name): array
    {
        return $this->getArray($name, ApiParameterType::Integer, true);
    }

    /**
     * @param string $name
     * @return string[]|null
     */
    public function getArrayOfString(string $name): ?array
    {
        return $this->getArray($name, ApiParameterType::String);
    }

    /**
     * @param string $name
     * @return string[]
     */
    public function requireArrayOfString(string $name): array
    {
        return $this->getArray($name, ApiParameterType::String, true);
    }

    public function __toString(): string
    {
        return http_build_query($this->data);
    }

    public function requireSelectionData(int $total_amount): SelectionData
    {
        if ($this->selection_options === null) {
            throw new RuntimeException('Selection options are empty');
        }

        $selection_data = new SelectionData(null, $this->selection_options->getOrdVariants());
        $selection_data->total_amount = $total_amount;

        $limit = $this->getInt('limit');
        if ($limit === null || $limit < 1 || $limit > $this->selection_options->requireLimitMax()) {
            $limit = App::settings('items_limit.default');
        }

        $selection_data->setLimit($limit);

        $ord_alias = $this->getString('ord');
        if ($ord_alias === null || $this->selection_options->getOrdVariant($ord_alias) === null) {
            $ord_alias = $this->selection_options->getOrdDefault();
        }

        $selection_data->setOrd($this->selection_options->getOrdVariant($ord_alias));

        $sort = null;
        $sort_value = $this->getString('sort');
        if ($sort_value !== null) {
            $sort = ApiSortDirection::make($sort_value);
        }

        $sort ??= $this->selection_options->sort_default;
        $selection_data->setSort($sort->value);

        $page = $this->getInt('page') ?? 1;
        $selection_data->setPage($page);

        return $selection_data;
    }

    public function addException(string $field_name, int $code): void
    {
        if (!array_key_exists($field_name, $this->parameters)) {
            throw new RuntimeException("API parameter with name $field_name not found");
        }

        $this->exception = new ApiException($code, $field_name, $this->exception);
    }

    public function getException(): ?ApiException
    {
        return $this->exception;
    }
    
}