<?php

function respondError($statusCode, $e = null)
{
    http_response_code($statusCode);

    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

    // Respuesta para API
    if (strpos($accept, 'application/json') !== false) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            "error" => "Error interno del servidor",
            "code" => "ERR{$statusCode}"
        ]);
    } else {
        // Respuesta para navegador
        header('Content-Type: text/html; charset=UTF-8');
        include __DIR__ . '/../../App/views/errors/internal-error.php';
    }
}

function handleException(Throwable $e)
{
    error_log($e); // log interno
    respondError(500, $e);
}

function handleError($severity, $message, $file, $line)
{
    throw new ErrorException($message, 0, $severity, $file, $line);
}

function handleShutdown()
{
    $error = error_get_last();
    if ($error !== null) {
        error_log(print_r($error, true));
        respondError(500);
    }
}

set_exception_handler('handleException');
set_error_handler('handleError');
register_shutdown_function('handleShutdown');
