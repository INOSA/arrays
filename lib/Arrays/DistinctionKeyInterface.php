<?php

declare(strict_types=1);

namespace Inosa\Arrays;

interface DistinctionKeyInterface
{
    /**
     * Return unique value to make list unique.
     * HINT: Key may be created from multiple properties to use multi-column unique filtration.
     */
    public function getDistinctionKey(): string;
}