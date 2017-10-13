<?php

if ( ! function_exists('config_path'))
{
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}
class_alias('Tymon\JWTAuth\Facades\JWTAuth', 'JWTAuth');
/** This gives you finer control over the payloads you create if you require it.
 *  Source: https://github.com/tymondesigns/jwt-auth/wiki/Installation
 */
class_alias('Tymon\JWTAuth\Facades\JWTFactory', 'JWTFactory'); // Optional