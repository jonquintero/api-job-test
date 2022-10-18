<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
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
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response|\Illuminate\Http\RedirectResponse
    {
        if ($e instanceof AuthenticationException) {
            if ($this->isFrontend($request)) {
                return redirect()->guest('login');
            }
            return $this->errorResponse(false, 'No se encuentra autenticado', 401);
        }

        if ($e instanceof AuthorizationException) {
            return $this->errorResponse(false, 'No posee permisos para ejecutar esta acción', 403);
        }

        if ($e instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($e->getModel()));
            return $this->errorResponse(false, "No existe ningun {$modelName} con ese identificador", 404);
        }

        if ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->errorResponse(false, 'No se encontró la URL especificada', 404);
        }
        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse(false, 'El método especificado en la petición no es válido', 405);
        }

        if ($e instanceof HttpException) {
            return $this->errorResponse(false, $e->getMessage(), $e->getStatusCode());
        }

        /********************* Habilitar en produccion *****************/
        /*  if(!$this->isFrontend($request)) {
              return $this->errorResponse(false, 'Error interno del servidor', 500);
          }*/

        if ($e instanceof QueryException) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1451) {
                return $this->errorResponse(false, 'No puede eliminar este registro. Tiene relacion con otro registro', 409);
            }
        }
        return parent::render($request, $e);
    }


    /**
     * @param ValidationException $e
     * @param $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response|\Illuminate\Http\RedirectResponse
    {
        $errors = $e->validator->errors()->getMessages();

        if ($this->isFrontend($request)) {
            return $request->ajax() ? response()->json($errors, 422) : redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($errors);
        }
        return $this->errorResponse(false, $errors, 422);
    }

    protected function errorResponse($boolean, $message, $code): \Illuminate\Http\JsonResponse
    {
        return response()->json(['success' => $boolean, 'error' => $message, 'code' => $code], $code);
    }

    private function isFrontend(Request $request): bool
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }

    private function successResponse($data, $code): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, $code);
    }
}
