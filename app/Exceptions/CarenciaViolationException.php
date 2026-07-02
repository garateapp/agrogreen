<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class CarenciaViolationException extends RuntimeException
{
    public function __construct(
        string $message = 'Violación de periodo de carencia fitosanitaria',
        int $code = 422,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
