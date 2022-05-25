<?php

declare(strict_types=1);

namespace App\Infrastructure\Arrays;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Inosa\Arrays\ArrayList;
use Inosa\Arrays\InvalidArrayHashMapException;
use Nette\Utils\Arrays;

/**
 * @template T
 */
class ArrayHashMap
{
    /**
     * @param Collection<T> $collection
     */
    private Collection $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public static function create(array $items): self
    {
        self::assertIsHashMap($items);

        return new self(new Collection($items));
    }

    public static function checkIfIsHashMap(array $items): bool
    {
        return count($items) === 0 || Arrays::isList($items) === false;
    }

    /**
     * @return T
     */
    public function get(string $key)
    {
        return Arr::get($this->collection, $key);
    }

    public function has(string $key): bool
    {
        return Arr::has($this->collection->toArray(), $key);
    }

    /**
     * @param T $value
     * @return self<T>
     */
    public function set(string $key, $value): self
    {
        $arr = $this->collection->toArray();
        Arr::set($arr, $key, $value);

        return new self(new Collection($arr));
    }

    public function hasAndNotNull(string $key): bool
    {
        return $this->collection->get($key) !== null;
    }

    public function put(string $key, mixed $value): void
    {
        $this->collection->put($key, $value);
    }

    /**
     * @param \Closure(T, string) $closure
     * @return self
     */
    public function transform(Closure $closure): self
    {
        return new self($this->collection->transform($closure));
    }

    /**
     * @param \Closure(self):mixed $closure
     * @return mixed
     */
    public function pipe(Closure $closure)
    {
        return $closure($this);
    }

    public function toArray(): array
    {
        return $this->collection->all();
    }

    public function isEmptyByKey(string $key): bool
    {
        if ($this->collection->has($key)) {
            return (new Collection($this->collection->get($key)))->isEmpty();
        }

        return true;
    }

    /**
     * @param callable $callable
     * @return self
     */
    public function each(callable $callable): self
    {
        return new self($this->collection->each($callable));
    }

    public function isEmpty(): bool
    {
        return $this->collection->isEmpty();
    }


    /**
     * @param \Closure(T, int) $closure
     * @return null|T
     */
    public function searchUsingFunction(Closure $closure)
    {
        $search = $this->collection->search($closure, false);

        if ($search === false) {
            return null;
        }

        return $this->collection->get($search);
    }

    /**
     * @param ArrayHashMap<T> $array
     * @return ArrayHashMap<T>
     */
    public function merge(ArrayHashMap $array): ArrayHashMap
    {
        return new self($this->collection->merge($array->toArray()));
    }

    /**
     * @return ArrayList
     */
    public function convertToList(): ArrayList
    {
        return ArrayList::create($this->collection->values()->all());
    }

    /**
     * @param self<T> $list
     * @return self<T>
     */
    public function intersectionByKeys(self $list): self
    {
        return new self($this->collection->intersectByKeys($list->toArray()));
    }

    /**
     * @return self<T>
     */
    public function remove(string $key): self
    {
        return new self($this->collection->forget($key));
    }

    public function filter(Closure $callback): self
    {
        return new self($this->collection->filter($callback));
    }

    public static function empty(): self
    {
        return self::create([]);
    }

    /**
     * @param array<string, mixed> $items
     */
    private static function assertIsHashMap(array $items): void
    {
        if (self::checkIfIsHashMap($items) === false) {
            throw InvalidArrayHashMapException::create();
        }
    }
}
