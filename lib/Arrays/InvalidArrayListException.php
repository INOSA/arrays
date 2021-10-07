<?php

declare(strict_types=1);

namespace Inosa\Arrays;

final class InvalidArrayListException extends \LogicException
{
    public static function create(): self
    {
        return new self('Tried to create invalid arrayList');
    }
}
