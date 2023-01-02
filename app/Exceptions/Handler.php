<?php

namespace App\Exceptions;

use App\Traits\apiResponseBuilder;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use apiResponseBuilder;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash =
    [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * @param Request $request
     * @param Throwable $exception
     * @return JsonResponse|Response
     * @throws Throwable
     */
    public function render( $request, $exception ) : JsonResponse|Response
    {
        if ( $exception instanceof ModelNotFoundException )
        {
            return $this -> errorResponse( array(), 'Error', 'Resource not found', Response::HTTP_NOT_FOUND );
        }
        elseif ( $exception instanceof MethodNotAllowedHttpException )
        {
            return $this -> errorResponse( array(), 'Error', 'You do not have permission to perform this action', Response::HTTP_METHOD_NOT_ALLOWED );
        }
        elseif ( $exception instanceof NotFoundHttpException )
        {
            return $this -> errorResponse( array(), 'Error', 'Resource not found', Response::HTTP_NOT_FOUND );
        }
        elseif ( $exception instanceof QueryException )
        {
            return $this -> errorResponse( array(), 'Error', 'Connection refused', Response::HTTP_UNAUTHORIZED );
        }
        elseif ( $exception instanceof RelationNotFoundException )
        {
            return $this -> errorResponse( array(), 'Error', 'Undefined relationship', Response::HTTP_INTERNAL_SERVER_ERROR );
        }
        elseif ( $exception instanceof AccessDeniedHttpException )
        {
            return $this -> errorResponse( array(), 'Error', 'This action is unauthorized.', Response::HTTP_FORBIDDEN );
        }

        return parent::render( $request, $exception );
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse|RedirectResponse|Response
     * @throws Throwable
     */
    protected function unauthenticated( $request, AuthenticationException $exception ) : JsonResponse|RedirectResponse|Response
    {
        if ( $request -> expectsJson() )
        {
            return $this -> errorResponse( array(), 'Error', 'User not authenticated', Response::HTTP_UNAUTHORIZED );
        }
        return redirect() -> guest('login');
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register() : void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
