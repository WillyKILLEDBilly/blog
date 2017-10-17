<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if (get_class($e) == 'Tymon\JWTAuth\Exceptions\TokenExpiredException') {
        return response()->json(['token_expired'], 404);
        } else if (get_class($e) == 'Tymon\JWTAuth\Exceptions\TokenInvalidException') {
            return response()->json(['token_invalid'], 404);
        } else if (get_class($e) == 'Tymon\JWTAuth\Exceptions\TokenBlacklistedException') {
            return response()->json(['token_blacklisted'], 404);
        }

        //return response()->json('page not found =(', 404);
        return parent::render($request, $e);
    }
}
