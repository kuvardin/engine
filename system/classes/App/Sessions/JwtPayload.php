<?php

declare(strict_types=1);

namespace App\Sessions;

use App;
use App\Api\v1\Exceptions\ApiException;
use App\Users\User;
use App\Users\UserRequiredTrait;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

class JwtPayload
{
    use UserRequiredTrait;
    use AuthorizationRequiredTrait;

    public const TYPE_ACCESS = 'access';
    public const TYPE_REFRESH = 'refresh';

    readonly public string $type;
    readonly public int $expiration_date;

    private function __construct(
        int $authorization_id,
        string $type,
        int $user_id,
        int $expiration_date,
    )
    {
        $this->sessions_authorization_id = $authorization_id;
        $this->type = $type;
        $this->user_id = $user_id;
        $this->expiration_date = $expiration_date;
    }

    /**
     * @throws ExpiredException
     */
    public static function make(string $jwt_token_value): ?self
    {
        try {
            $jwt_decoded = JWT::decode(
                $jwt_token_value,
                new Key(App::settings('jwt.key'), App::settings('jwt.algorithm'))
            );

            if (!property_exists($jwt_decoded, 'id') || !is_int($jwt_decoded->id)) {
                return null;
            }

            if (!property_exists($jwt_decoded, 'type') || !is_string($jwt_decoded->type) ||
                !self::checkType($jwt_decoded->type)) {
                return null;
            }

            if (!property_exists($jwt_decoded, 'user') || !is_int($jwt_decoded->user)) {
                return null;
            }

            if (!property_exists($jwt_decoded, 'exp') || !is_int($jwt_decoded->exp)) {
                return null;
            }

            $hash = self::getHash($jwt_decoded->id, $jwt_decoded->user, $jwt_decoded->exp);
            if (!property_exists($jwt_decoded, 'secret') || !is_string($jwt_decoded->secret) ||
                $jwt_decoded->secret !== $hash) {
                return null;
            }

            return new self(
                $jwt_decoded->id,
                $jwt_decoded->type,
                $jwt_decoded->user,
                $jwt_decoded->exp,
            );
        } catch (SignatureInvalidException) {
            return null;
        }
    }

    private static function getHash(
        int $authorization_id,
        int $user_id,
        int $expiration_date,
    ): string
    {
        return md5("AUTH{$authorization_id}USER{$user_id}EXP{$expiration_date}SALT" . App::settings('jwt.salt'));
    }

    public static function getAccessToken(Authorization $authorization): string
    {
        $payload = [
            'id' => $authorization->getId(),
            'type' => self::TYPE_ACCESS,
            'secret' => self::getHash(
                $authorization->getId(),
                $authorization->getUserId(),
                $authorization->getAccessTokenExpirationDate(),
            ),
            'user' => $authorization->getUserId(),
            'exp' => $authorization->getAccessTokenExpirationDate(),
        ];

        return JWT::encode($payload, App::settings('jwt.key'), App::settings('jwt.algorithm'));
    }

    public static function getRefreshToken(Authorization $authorization): string
    {
        $payload = [
            'id' => $authorization->getId(),
            'type' => self::TYPE_REFRESH,
            'secret' => self::getHash(
                $authorization->getId(),
                $authorization->getUserId(),
                $authorization->getRefreshTokenExpirationDate(),
            ),
            'user' => $authorization->getUserId(),
            'exp' => $authorization->getRefreshTokenExpirationDate(),
        ];

        return JWT::encode($payload, App::settings('jwt.key'), App::settings('jwt.algorithm'));
    }

    public static function checkType(string $type): bool
    {
        return $type === self::TYPE_ACCESS || $type === self::TYPE_REFRESH;
    }
}