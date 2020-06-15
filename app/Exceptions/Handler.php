<?php

namespace App\Exceptions;

use Exception;
use App\Traits\ApiResponderTrait;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException as AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException as AuthorizationException;
use Illuminate\Validation\ValidationException as ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException as MethodNotAllowed;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException as NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException as HttpException;
use App\Exceptions\CustomException as CustomException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponderTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        // Get an ovsettings value
        $api_exception_handler = config('ovsettings.api_exception_handler', false);

        if ($request->expectsJson() && $api_exception_handler) {

            // Thrown when an error occurs when a user makes an unauthenticated request
            if ($exception instanceof AuthenticationException) {
                return $this->authenticationFailure();
            }

            // Thrown when a user makes requests that Auth service does not validated
            if ($exception instanceof AuthorizationException) {
                return $this->forbiddenAccess();
            }

            // Thrown when the request fails Laravel FormValidator validation.
            if ($exception instanceof ValidationException) {
                return $this->formProcessingFailure($exception->errors(),'Inappropriate input');
            }

            // Thrown when HTTP Method is incorrect when requesting routing
            if ($exception instanceof MethodNotAllowed) {
                return $this->wrongRequestType($exception->getMessage());
            }

            // Thrown when the HTTP requested route can not be found
            if ($exception instanceof NotFoundHttpException) {
                return $this->notFound();
            }

            // Thrown when processing HTTP requests is unsuccessful
            if ($exception instanceof HttpException) {
                return $this->unavailableService();
            }

            // Thrown when a custom exception occurs.
            if ($exception instanceof CustomException) {
                return $this->internalServerError($exception->getMessage());
            }

            // Thrown when an exception occurs.
            if ($exception instanceof Exception) {
                return $this->internalServerError();
            }
        }

        return parent::render($request, $exception);
    }
}