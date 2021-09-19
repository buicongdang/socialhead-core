<?php
namespace Socialhead\Core\Helper;

use Firebase\JWT\JWT;

class Socialhead
{
    static function encodeToken($arg)
    {
        return JWT::encode($arg, config('socialhead.app.jwt_token'), 'HS256');
    }

    static function decodeToken($token)
    {
        return JWT::decode($token, config('socialhead.app.jwt_token'), ['HS256']);
    }
}
