<?php

declare(strict_types=1);

namespace App\Api\v1;

use App\Api\v1\Output\ApiField;
use App\Api\v1\Output\ApiFieldType;
use App\Languages\Language;
use App\Sites\TemplatesEngine\Alert;
use App\Sites\TemplatesEngine\BootstrapColor;
use App\Sites\TemplatesEngine\Page;
use Throwable;

class ApiReflection
{
    protected const API_MODELS_DIR = CLASSES_DIR . '/App/Api/v1/Models';
    protected const API_MODELS_NAMESPACE = 'App\\Api\\v1\\Models';

    protected const API_METHODS_DIR = CLASSES_DIR . '/App/Api/v1/Methods';
    protected const API_METHODS_NAMESPACE = 'App\\Api\\v1\\Methods';

    private function __construct()
    {
    }

    /**
     * @param string[]|ApiMethod[] $result
     * @param string[] $errors
     * @param string[] $parents
     * @return ApiMethod[]|string[]
     */
    public static function getApiMethods(array &$result, array &$errors, array $parents = []): array
    {
        $directory = self::API_METHODS_DIR . '/' . implode('/', $parents);
        $files = scandir($directory);

        foreach ($files as $file_path) {
            if ($file_path === '.' || $file_path === '..') {
                continue;
            }

            if (is_dir($directory . '/' . $file_path)) {
                self::getApiMethods($result, $errors, array_merge($parents, [$file_path]));
                continue;
            }

            if (!preg_match('|^(.+?)\.php$|', $file_path, $match)) {
                $errors[] = "Incorrect method class file name: $file_path";
                continue;
            }

            $method_name = $match[1];
            $method_full_path = $directory . '/' . $file_path;

            /** @var ApiMethod|string $method_class */
            $method_class = $parents === []
                ? self::API_METHODS_NAMESPACE . '\\' . $method_name
                : self::API_METHODS_NAMESPACE . '\\' . implode('\\', $parents) . '\\' . $method_name;

            try {
                $success = include $method_full_path;
                $method_class::getResultField();

                $method_public_name = '';
                foreach ($parents as $parent) {
                    $method_public_name .= lcfirst($parent) . '/';
                }

                $method_public_name .= lcfirst($method_name);
                $result[$method_public_name] = $method_class;
            } catch (Throwable $exception) {
                $errors[] = "Method class $file_path has error: {$exception->getMessage()}";
                continue;
            }
        }

        return $result;
    }

    public static function getApiModels(array &$errors, Language $lang): array
    {
        /**
         * @var string[]|ApiModel[] $result
         */
        $result = [];

        $files = scandir(self::API_MODELS_DIR);
        foreach ($files as $file_path) {
            if (is_dir(self::API_MODELS_DIR . '/' . $file_path)) {
                continue;
            }

            if (!preg_match('|^(.+?)ApiModel\.php$|', $file_path, $match)) {
                $errors[] = "Incorrect model class name: $file_path";
                continue;
            }

            $model_name = $match[1];
            $model_full_path = self::API_MODELS_DIR . '/' . $file_path;
            try {
                $success = require_once $model_full_path;

                /** @var ApiModel|string|null $model_class */
                $model_class = self::API_MODELS_NAMESPACE . '\\'. $model_name . 'ApiModel';

                $model_class::getFields();

                $result[$model_name] = $model_class;
            } catch (Throwable $exception) {
                $errors[] = "Model class $file_path has error: {$exception->getMessage()}";
                continue;
            }
        }

        return $result;
    }
}