<?php

declare(strict_types=1);

namespace App\Languages;

trait LanguageByCodeRequiredTrait
{
    protected string $language_code;
    protected ?Language $language = null;

    public function getLanguageCode(): string
    {
        return $this->language_code;
    }

    public function getLanguage(): Language
    {
        if ($this->language !== null && $this->language->getCode() === $this->language_code) {
            return $this->language;
        }

        return $this->language ??= new Language($this->language_code);
    }
}