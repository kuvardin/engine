<?php

declare(strict_types=1);

namespace App\Languages;

use App;
use RuntimeException;

class Phrase
{
    /**
     * @var string[]
     */
    protected array $values;

    public static function make(string $language_code, string $value): self
    {
        $result = new self;
        $result->setValue($language_code, $value);
        return $result;
    }

    public static function makeFromArray(array $array): self
    {
        $result = new self;
        foreach ($array as $language_code => $value) {
            $result->setValue($language_code, $value);
        }

        return $result;
    }

    public function getValue(string $lang_code): ?string
    {
        if (!Language::checkLangCode($lang_code)) {
            throw new RuntimeException("Unknown language code: $lang_code");
        }

        return $this->values[$lang_code] ?? null;
    }

    public function setValue(string $lang_code, ?string $value): self
    {
        if (!Language::checkLangCode($lang_code)) {
            throw new RuntimeException("Unknown language code: $lang_code");
        }

        $this->values[$lang_code] = $value === '' ? null : $value;
        return $this;
    }

    public function isEmpty(): bool
    {
        foreach ($this->values as $value) {
            if ($value !== null) {
                return false;
            }
        }

        return true;
    }

    public function throwErrorIfEmpty(): void
    {
        if ($this->isEmpty()) {
            throw new RuntimeException('Empty phrase');
        }
    }

    /**
     * @return string[]
     */
    public function getArray(): array
    {
        return $this->values;
    }

    public function getInfo(int $max_length = null): string
    {
        if ($this->isEmpty()) {
            return 'EMPTY_PHRASE';
        }

        $one_max_length = null;

        if ($max_length !== null) {
            $languages = 0;
            $total_length = 0;

            foreach ($this->values as $value) {
                if ($value !== null) {
                    $languages++;
                    $total_length += mb_strlen($value);
                }
            }

            if ($total_length > $max_length) {
                $one_max_length = (int)($max_length / $languages);
            }
        }

        $result = [];
        foreach ($this->values as $lang_code => $value) {
            $result[] = $lang_code . ': ' .
                ($one_max_length === null ? $value : App::shortenString($value, $one_max_length));
        }

        return implode(' | ', $result);
    }

    public function getFieldsArray(string $prefix, string $delimiter = '_', array $codes = null): array
    {
        $result = [];

        foreach (($codes ?? App::settings('languages')) as $lang_code) {
            if (isset($this->values[$lang_code])) {
                $result[$prefix.$delimiter.$lang_code] = $this->values[$lang_code];
            }
        }

        return $result;
    }
}