<?php

declare(strict_types=1);

namespace App\Arrays;

use Exception;
use Throwable;

final class FirstElementDoesNotExistsException extends Exception
{
    public function __construct(int $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            "Accessing first element in the list failed. First element does not exists.",
            $code,
            $previous
        );
    }
}
