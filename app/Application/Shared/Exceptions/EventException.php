<?php

namespace App\Application\Shared\Exceptions;

class EventException extends \Exception
{
    public function __construct(
        string $message = "Tadbir bilan bog'liq xatolik yuz berdi",
        int $code = 400,
        ?\Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
