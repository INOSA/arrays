<?php

declare(strict_types=1);

namespace App\Arrays;

final class ArrayKeyNotFoundException extends \LogicException
{
    /**
     * @param string|int $key
     */
    public static function create($key): self
    {
        return new self("Array key: $key not found in array");
    }
}
