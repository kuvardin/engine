<?php

declare(strict_types=1);

namespace App\Api\v1;

use App;
use App\Api\ApiVersionController as ApiVersionControllerAbstract;
use App\Api\v1\Exceptions\ApiException;
use App\Api\v1\Exceptions\IncorrectFieldValueException;
use App\Api\v1\Input\ApiInput;
use App\Api\v1\Models\ErrorApiModel;
use App\Api\v1\Output\ApiField;
use App\Api\v1\Output\ApiFieldType;
use App\Exceptions\NotEnoughRightsException;
use App\Logger;
use App\Sessions\JwtPayload;
use App\Sessions\Session;
use App\Sessions\WebBot;
use App\Telegram\Notifier;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use JsonException;
use Kuvardin\FieldsScanner\Field;
use RuntimeException;
use Throwable;
use UnexpectedValueException;

class ApiVersionController extends ApiVersionControllerAbstract
{
    protected const INPUT_FIELD_SESSION_ID = 'session_id';

    public static function handle(
        array $route_parts,
        array $get,
        array $post,
        array $cookies,
        array $files,
        ?string $input_string,
        string $ip,
        ?string $user_agent,
    ): void
    {
        App::connectMysqli();
        App::mysqli()->write_log_to_variable = true;

        $input_data = [];
        if ($get !== []) {
            $input_data = $get;
        } elseif ($post !== []) {
            $input_data = $post;
        } elseif ($input_string !== null) {
            try {
                $input_data_decoded = json_decode($input_string, true, 512, JSON_THROW_ON_ERROR);
                if (is_array($input_data_decoded)) {
                    $input_data = $input_data_decoded;
                }
            } catch (JsonException) {

            }
        }

        /** @var Throwable|null $last_exception */
        $last_exception = null;

        $route = implode('/', $route_parts);

        try {
            try {
                $web_bot_code = $user_agent === null ? null : WebBot::makeByUserAgent($user_agent);
                $session = $web_bot_code === null
                    ? Session::makeByCookies($cookies, $ip, $user_agent)
                    : Session::makeByWebBotCode($web_bot_code->value);

                if ($session === null && !empty($input_data[self::INPUT_FIELD_SESSION_ID])) {
                    $session = Session::makeBySecretCode($input_data[self::INPUT_FIELD_SESSION_ID]);
                }

                if ($session === null) {
                    $session = Session::create($ip, $user_agent, App::settings('language.default'));
                } else {
                    $session->fixRequest($ip, $user_agent);
                    $session->save();
                }

                $session->setCookie();

                if ($route !== 'refreshAuthorization' && $route !== 'quit') {
                    $jwt_payload = null;
                    if (!empty($_SERVER['HTTP_AUTHORIZATION']) && preg_match('|^Bearer\s+(.+)$|i',
                            $_SERVER['HTTP_AUTHORIZATION'], $http_authorization_match)) {
                        $jwt_token_value = trim($http_authorization_match[1]);
                        if ($jwt_token_value !== '') {
                            try {
                                $jwt_payload = JwtPayload::make($jwt_token_value);
                            } catch (ExpiredException) {
                                throw new ApiException(1004);
                            } catch (UnexpectedValueException) {
                                throw new ApiException(1003);
                            }

                            if ($jwt_payload === null || $jwt_payload->type !== JwtPayload::TYPE_ACCESS) {
                                throw new ApiException(1003);
                            }
                        }
                    }

                    if ($route !== 'authorization') {
                        if ($jwt_payload === null) {
                            if ($session->isAuthorized()) {
                                throw new ApiException(1003);
                            }
                        } else {
                            if ($jwt_payload->getSessionsAuthorization()->isDeleted()) {
                                throw new ApiException(1004);
                            }

                            if ($jwt_payload->getUserId() !== $session->getUserId()) {
                                throw new ApiException(1004);
                            }
                        }
                    }
                }

                $lang = $session->getLanguage();

                $method_class = self::getMethodClass($route_parts);
                if ($method_class === null || !class_exists($method_class)) {
                    throw new ApiException(1002);
                }

                $input = new ApiInput(
                    $method_class::getAllParameters($lang),
                    $input_data,
                    $files,
                    $session->getLanguageCode(),
                    $method_class::getSelectionOptions($lang),
                );

                if ($input->getException() !== null) {
                    throw $input->getException();
                }

                $method_result = $method_class::isMutable()
                    ? $method_class::handle($input, $session)
                    : $method_class::handle($input);

                $method_result_field = $method_class::getResultField();

                $public_data = null;
                if ($method_result === null) {
                    if ($method_result_field !== null && !$method_result_field->nullable) {
                        throw new RuntimeException("Method $method_class returns null");
                    }
                } else {
                    if ($method_result_field === null) {
                        throw new RuntimeException("Method $method_class must return null");
                    }

                    $public_data = self::processResult($session, $method_result_field, $method_result, null, 'result');
                }

                $result = [
                    'ok' => true,
                    'result' => $public_data,
                    'errors' => [],
                ];
            } catch (NotEnoughRightsException $exception) {
                throw new ApiException(ApiException::NOT_ENOUGH_RIGHTS, previous: $exception);
            } catch (ApiException $exception) {
                throw $exception;
            } catch (Throwable $exception) {
                $last_exception = $exception;
                Logger::writeException($exception);
                Notifier::tryToSendException($exception);
                throw new ApiException(ApiException::INTERNAL_SERVER_ERROR, previous: $exception);
            }
        } catch (ApiException $api_exception) {
            $exceptions_public_data = [];
            do {
                if ($api_exception instanceof ApiException) {
                    $exceptions_public_data[] = (new ErrorApiModel($api_exception))->getPublicData();
                }
            } while ($api_exception = $api_exception->getPrevious());

            $result = [
                'ok' => false,
                'result' => null,
                'errors' => $exceptions_public_data,
            ];
        }

        $input_data_string = isset($input) ? (string)$input : '';
        if ($input_data_string === '') {
            $input_data_string = null;
        }

//        if (isset($session) && $session->getUser()?->getUsersPostId() === 1) {
            $result['debug'] = [
                'gen_time' => App::getGenerationTime(),
                'input' => $input_data_string,
                'input_hash' => $input_data_string === null ? null : md5($input_data_string),
                'mysql_requests' => App::mysqli()->fast_queries_number(),
                'mysql_log' => App::mysqli()->log,
                'exception' => $last_exception === null ? null : (string)$last_exception,
            ];
//        }

        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Expires: ' . date('r'));
        header('Content-Type: application/json');
        echo json_encode($result, JSON_THROW_ON_ERROR);
    }

    protected static function processResult(
        Session $session,
        ApiField $field,
        mixed $value,
        ApiModel|string|null $model_class,
        string $field_name,
    ): mixed
    {
        $field_name_full = ($model_class === null ? '' : "$model_class -> ") . $field_name;

        if ($value === null) {
            if (!$field->nullable) {
                throw new RuntimeException("Field $field_name_full are null but not nullable");
            }

            return null;
        }

        switch ($field->type) {
            case ApiFieldType::String:
                if (!is_string($value)) {
                    throw new IncorrectFieldValueException($field_name, $model_class, $field, $value);
                }

                return $value;

            case ApiFieldType::Timestamp:
            case ApiFieldType::Integer:
                if (!is_int($value)) {
                    throw new IncorrectFieldValueException($field_name, $model_class, $field, $value);
                }

                return $value;

            case ApiFieldType::Float:
                if (!is_float($value)) {
                    throw new IncorrectFieldValueException($field_name, $model_class, $field, $value);
                }

                return $value;

            case ApiFieldType::Boolean:
                if (!is_bool($value)) {
                    throw new IncorrectFieldValueException($field_name, $model_class, $field, $value);
                }

                return $value;

            case ApiFieldType::Object:
                if (!is_object($value) && !($value instanceof $field->model_class)) {
                    throw new IncorrectFieldValueException($field_name, $model_class, $field, $value);
                }

                $model_fields = $field->model_class::getFields();

                $result = [];
                $public_data = $value->getPublicData($session);
                foreach ($public_data as $public_data_field => $public_data_value) {
                    if (!array_key_exists($public_data_field, $model_fields)) {
                        throw new RuntimeException("Unknown {$field->model_class} field named $public_data_field");
                    }

                    $result[$public_data_field] = self::processResult(
                        $session,
                        $model_fields[$public_data_field],
                        $public_data_value,
                        $field->model_class,
                        $public_data_field,
                    );
                }

                foreach ($model_fields as $model_field_name => $model_field) {
                    if (!array_key_exists($model_field_name, $public_data)) {
                        throw new RuntimeException("Field $model_field_name not found in {$field->model_class}");
                    }
                }

                return $result;

            case ApiFieldType::Phrase:
                if (!is_array($value)) {
                    throw new IncorrectFieldValueException($field_name, $model_class, $field, $value);
                }

                return $value;

            case ApiFieldType::Array:
                $result = [];
                $child_field = $field->array_child_type === ApiFieldType::Object
                    ? ApiField::object($field->array_child_model_class, false)
                    : ApiField::scalar($field->array_child_type, false);

                foreach ($value as $array_item_key => $array_item_value) {
                    if (!is_int($array_item_key)) {
                        throw new RuntimeException("Array $field_name_full must be not associative");
                    }

                    $result[] = self::processResult(
                        $session,
                        $child_field,
                        $array_item_value,
                        $field->array_child_model_class,
                        '[array_item]',
                    );
                }

                return $result;

            default:
                throw new RuntimeException("Unknown type {$field->type->value}");
        }
    }

    public static function getMethodClass(array $route_parts): string|ApiMethod|null
    {
        $result = 'App\\Api\\v1\\Methods';
        foreach ($route_parts as $route_part) {
            if ($route_part === '') {
                continue;
            }

            $route_part_ucfirst = ucfirst($route_part);
            if ($route_part_ucfirst === $route_part) {
                return null;
            }

            $result .= '\\' . $route_part_ucfirst;
        }

        return $result;
    }
}