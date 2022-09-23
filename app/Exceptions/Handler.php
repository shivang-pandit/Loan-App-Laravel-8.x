<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $e)
    {
        if($e instanceof NotFoundHttpException){
            return response()->json([
                "code" => 404,
                "status" => "Error",
                "message" => "Something went wrong!",
            ],404);
        }

        if ($e instanceof MissingAbilityException) {
            return response()->json([
                "code" => 401,
                "status" => "Unauthorized",
                "message" => "Invalid Access token!",
            ],401);
        }

        if ($e instanceof AuthenticationException) {
            return response()->json([
                "code" => 401,
                "status" => "Unauthorized",
                "message" => "Invalid Access token!",
            ],401);
        }

        if ($e instanceof RouteNotFoundException) {
            return response()->json([
                "code" => 401,
                "status" => "Unauthorized",
                "message" => "Access token is required!",
            ],401);
        }

        if ( ($e instanceof HttpException))
        {
            switch ($e->getStatusCode()) {

                // not authorized
                case '403':
                    return response()->json([
                        "code" => 403,
                        "status" => "error",
                        "message" => "You are not authorized to perform this action!",
                    ],403);
                    break;

                // not found
                case '404':
                    return response()->json([
                        "code" => 404,
                        "status" => "error",
                        "message" => "Space invaders took this page away. You should take revenge!",
                    ],404);
                    break;

                // internal error
                case '500':
                    return response()->json([
                        "code" => $e->getStatusCode(),
                        "status" => "error",
                        "message" => "Team of expert monkeys have been deployed to resolve the issue. Please try again sometime later.",
                    ],500);
                    break;

                default:
                    return response()->json([
                        "code" => 500,
                        "status" => "error",
                        "message" => "Congratulations! You broke the internet.",
                    ],500);
                    break;
            }
        }

        if ( $e instanceof \ErrorException ) {
            return response()->json([
                "code" => 500,
                "status" => "error",
                "message" => "Congratulations! You broke the internet.",
            ],500);
        }

        return parent::render($request,$e);
    }
}
