<?php

namespace App\Application\Shared\Exceptions;

class EventNotFoundException extends \Exception
{
    public function __construct(
        string $message = "Tadbir topilmadi"
    )
    {
        parent::__construct($message, 404);
    }
}
