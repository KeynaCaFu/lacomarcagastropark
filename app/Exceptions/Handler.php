<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;
use PDOException;
use Throwable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Manejar errores de conexión a la base de datos
        $this->renderable(function (Throwable $e, Request $request) {
            // Detectar errores de conexión a BD
            if ($this->isDbConnectionError($e)) {
                // Verificar si es un error de conexión específicamente
                if ($this->isConnectionRefused($e)) {
                    return response()->view('errors.db-connection', [
                        // No pasar excepción en producción por seguridad
                        'exception' => env('APP_DEBUG') ? $e : null,
                    ], 503);
                }
                
                return response()->view('errors.connection-error', [
                    'exception' => env('APP_DEBUG') ? $e : null,
                    'code' => 503,
                    'title' => 'Error de Conexión a Base de Datos',
                    'message' => 'No podemos conectar con la base de datos. Por favor, intenta de nuevo en unos momentos.',
                ], 503);
            }

            // Detectar errores de red/conexión a internet
            if ($this->isNetworkError($e)) {
                return response()->view('errors.no-internet', [
                    'exception' => env('APP_DEBUG') ? $e : null,
                ], 503);
            }

            // Errores 503 de servidor general
            if ($e instanceof HttpException && $e->getStatusCode() === 503) {
                return response()->view('errors.connection-error', [
                    'exception' => env('APP_DEBUG') ? $e : null,
                    'code' => 503,
                    'title' => 'Servicio No Disponible',
                    'message' => 'El servicio no está disponible en este momento.',
                ], 503);
            }

            return null;
        });
    }

    /**
     * Verificar si es un error de conexión a base de datos.
     *
     * @param Throwable $e
     * @return bool
     */
    protected function isDbConnectionError(Throwable $e): bool
    {
        if ($e instanceof QueryException || $e instanceof PDOException) {
            return true;
        }

        // Verificar por mensajes de error comunes
        $message = $e->getMessage();
        $dbErrors = [
            'connection refused',
            'connection timeout',
            'SQLSTATE',
            'database connection',
            'access denied',
            'unknown database',
            'no connection',
            'cannot connect',
        ];

        foreach ($dbErrors as $error) {
            if (stripos($message, $error) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar si la conexión fue rechazada.
     *
     * @param Throwable $e
     * @return bool
     */
    protected function isConnectionRefused(Throwable $e): bool
    {
        $message = strtolower($e->getMessage());
        return strpos($message, 'connection refused') !== false || 
               strpos($message, 'no connection') !== false ||
               strpos($message, 'cannot connect') !== false;
    }

    /**
     * Verificar si es un error de red.
     *
     * @param Throwable $e
     * @return bool
     */
    protected function isNetworkError(Throwable $e): bool
    {
        $message = strtolower($e->getMessage());
        $networkErrors = [
            'network unreachable',
            'network is unreachable',
            'no route to host',
            'connection timed out',
            'nodename nor servname provided',
        ];

        foreach ($networkErrors as $error) {
            if (strpos($message, $error) !== false) {
                return true;
            }
        }

        return false;
    }
}
