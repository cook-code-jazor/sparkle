<?php


namespace Sparkle\Auth;


use Sparkle\Auth\Jwt\JwtToken;
use Sparkle\Http\HttpException;

class Authenticate
{

    /**
     * @param $credentials
     * @param $class
     * @throws HttpException
     */
    public static function auth($credentials, $class)
    {
        $password = $credentials['password'];
        unset($credentials['password']);
        if (!$password) {
            throw new HttpException(400, 'password required');
        }

        $authenticatable = $class::where($credentials)->find();
        if (!$authenticatable) {
            throw new HttpException(400, 'credentials is invalid');
        }
        if (password_verify($password, $authenticatable->getAuthPassword())) {
            throw new HttpException(403, 'password is invalid');
        }
        return $authenticatable;
    }

    /**
     * @param $token
     * @param $class
     * @return mixed
     * @throws HttpException
     */
    public static function verify($token, $class)
    {
        try {
            $payload = JwtToken::verify($token);
            return (new $class())->getAuthenticatable($payload);
        } catch (\Exception $e) {
            throw new HttpException(401, 'access_token is invalid');
        }
    }
}
