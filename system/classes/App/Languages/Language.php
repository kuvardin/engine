<?php

declare(strict_types=1);

namespace App\Languages;

use App;
use RuntimeException;

class Language
{
    public const MAIN_PHRASE_IN_CURRENT_LANGUAGE = '__in_current_language';
    public const MAIN_PHRASE_FLAG = '__flag';

    /** Русский язык */
    public const LANGUAGE_RU = 'ru';

    /** Казахский язык */
    public const LANGUAGE_KK = 'kk';

    /** Кыргызский язык */
    public const LANGUAGE_KG = 'kg';

    /** Английский язык */
    public const LANGUAGE_EN = 'en';

    protected const MAIN_PHRASES = [
        self::MAIN_PHRASE_IN_CURRENT_LANGUAGE => [
            self::LANGUAGE_RU => 'На русском',
            self::LANGUAGE_KK => 'Қазақша',
            self::LANGUAGE_KG => 'Кыргызча',
            self::LANGUAGE_EN => 'In English',
        ],
        self::MAIN_PHRASE_FLAG => [
            self::LANGUAGE_RU => "\xf0\x9f\x87\xb7\xf0\x9f\x87\xba",
            self::LANGUAGE_KK => "\xf0\x9f\x87\xb0\xf0\x9f\x87\xbf",
            self::LANGUAGE_KG => "\xf0\x9f\x87\xb0\xf0\x9f\x87\xbf",
            self::LANGUAGE_EN => "\xf0\x9f\x87\xac\xf0\x9f\x87\xa7",
        ],
    ];

    /**
     * @var string[]
     */
    protected array $phrases = [];

    /**
     * @var string[]
     */
    protected array $phrases_alternative = [];

    protected string $code;
    protected bool $test_mode;

    public function __construct(string $language_code, bool $test_mode = false)
    {
        if (!self::checkLangCode($language_code)) {
            throw new RuntimeException("Unknown language: {$language_code}");
        }

        $this->code = $language_code;
        $this->phrases = self::MAIN_PHRASES[$language_code] ?? [];
        $this->test_mode = $test_mode;
    }

    public static function checkLangCode(string $lang_code = null): bool
    {
        return $lang_code !== null && in_array($lang_code, App::settings('languages'), true);
    }

    /**
     * @return string[]
     */
    public static function getPhraseArray(array $data, string $prefix, string $delimiter = null,
        array $codes = null): array
    {
        $result = [];
        foreach (($codes ?? App::settings('languages')) as $lang_code) {
            $key = $prefix . ($delimiter ?? '_') . $lang_code;
            if (array_key_exists($key, $data)) {
                $result[$lang_code] = $data[$key];
            }
        }

        return $result;
    }

    private static function addVariables(string $string, array $vars): string
    {
        foreach ($vars as $key => $value) {
            $string = str_replace(
                '{' . $key. '}',
                is_int($value) | is_float($value) ? (string)$value : $value,
                $string
            );
        }

        return $string;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function get(string $phrase_name, array $arguments = null, array $vars = null): ?string
    {
        if (!isset($this->phrases[$phrase_name])) {
            return null;
        }

        $result = $arguments === null
            ? $this->phrases[$phrase_name]
            : sprintf($this->phrases[$phrase_name], ...$arguments);


        if (!empty($vars)) {
            $result = self::addVariables($result, $vars);
        }

        return ($this->test_mode ? "$phrase_name: " : '') . $result;
    }

    public function require(string $phrase_name, array $arguments = null, array $vars = null): string
    {
        $phrase = $this->phrases[$phrase_name] ?? $this->phrases_alternative[$phrase_name];
        $result = $arguments === null ? $phrase : sprintf($phrase, ...$arguments);

        if (!empty($vars)) {
            $result = self::addVariables($result, $vars);
        }

        return ($this->test_mode ? "$phrase_name: " : '') . $result;
    }

    public function phraseExists(string $phrase_name): bool
    {
        return array_key_exists($phrase_name, $this->phrases) ||
            array_key_exists($phrase_name, $this->phrases_alternative);
    }

    public function requireFrom(array $phrases, array $arguments = null): string
    {
        return $this->getFrom($phrases, $arguments);
    }

    public function getFrom(array $phrases, array $arguments = null): ?string
    {
        $phrase = null;
        if (!empty($phrases[$this->code])) {
            $phrase = $phrases[$this->code];
        } else {
            foreach (App::settings('languages') as $lang_code) {
                if (!empty($phrases[$lang_code])) {
                    $phrase = $phrases[$lang_code];
                    break;
                }
            }
        }

        if ($phrase === null) {
            return null;
        }

        return $arguments === null ? $phrase : sprintf($phrase, ...$arguments);
    }

    public function requireOrNullFrom(array $phrases, array $arguments = null): ?string
    {
        $phrase = null;
        if (!empty($phrases[$this->code])) {
            $phrase = $phrases[$this->code];
        } else {
            foreach (App::settings('languages') as $lang_code) {
                if (!empty($phrases[$lang_code])) {
                    $phrase = $phrases[$lang_code];
                    break;
                }
            }
        }

        if ($phrase === null) {
            return null;
        }

        return $arguments === null ? $phrase : sprintf($phrase, ...$arguments);
    }

    public function getPhrases(): array
    {
        return array_merge($this->phrases, $this->phrases_alternative);
    }

    public function setPhrases(array $phrases): void
    {
        foreach ($phrases as $phrase_name => $phrase_in_languages) {
            if (!empty($phrase_in_languages[$this->code])) {
                if (!is_string($phrase_in_languages[$this->code])) {
                    $type = gettype($phrase_in_languages[$this->code]);
                    throw new RuntimeException("Incorrect phrase $phrase_name in language {$this->code} typed $type: " .
                        print_r($phrase_in_languages[$this->code], true));
                }
                $this->phrases[$phrase_name] = $phrase_in_languages[$this->code];
            } else {
                $found = false;
                foreach (App::settings('languages') as $lang_code) {
                    if (!empty($phrase_in_languages[$lang_code])) {
                        if (!is_string($phrase_in_languages[$lang_code])) {
                            $type = gettype($phrase_in_languages[$lang_code]);
                            throw new RuntimeException("Incorrect phrase $phrase_name in language {$lang_code} " .
                                "typed $type: " . print_r($phrase_in_languages[$lang_code], true));
                        }

                        $this->phrases_alternative[$phrase_name] = $phrase_in_languages[$lang_code];
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    throw new RuntimeException("Phrase named $phrase_name not found");
                }
            }
        }
    }

    public static function getMainPhrase(string $name, string $language_code): string
    {
        return self::MAIN_PHRASES[$name][$language_code];
    }
}