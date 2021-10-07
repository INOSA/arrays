<?php

declare(strict_types=1);

namespace App\Arrays;

final class InvalidArrayHashMapException extends \LogicException
{
    public static function create(): self
    {
        return new self('Tried to create invalid Array hash map');
    }
}
