<?php

/** @noinspection PhpRedundantDocCommentInspection */

declare(strict_types=1);

namespace Inosa\Arrays;

use ArrayIterator;
use Closure;
use Illuminate\Support\Collection;
use IteratorAggregate;
use Nette\Utils\Arrays;

/**
 * @template T
 */
class ArrayList implements IteratorAggregate
{
    protected Collection $collection;

    protected function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @param array<int, mixed> $items
     * @return ArrayList
     */
    public static function create(array $items): self
    {
        self::assertIsList($items);

        return new self(new Collection($items));
    }

    public function filter(Closure $callback): self
    {
        return new self($this->collection->filter($callback));
    }

    public function sortByFunction(Closure $field): self
    {
        return new self($this->collection->sortBy($field));
    }

    /**
     * Returns a new collection with the keys reset to consecutive integers.
     *
     * @return self
     */
    public function values(): self
    {
        return new self($this->collection->values());
    }

    /**
     * @return T
     */
    public function get(int $key)
    {
        return $this->collection->get($key);
    }

    public function has(int $key): bool
    {
        return $this->collection->has($key);
    }

    /**
     * @param T $item
     */
    public function push($item): void
    {
        $this->collection->push($item);
    }

    /**
     * @param T $item
     */
    public function inArray($item): bool
    {
        return $this->collection->containsStrict($item);
    }

    /**
     * @param T $item
     * @return null|T
     */
    public function search($item)
    {
        $search = $this->collection->search($item, false);

        if (false === $search) {
            return null;
        }

        return $this->collection->get($search);
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
     * @param \Closure(T, int) $closure
     * @return self
     */
    public function transform(Closure $closure): self
    {
        $r = $this->collection->map($closure)->all();

        return new self(new Collection($r));
    }

    /**
     * @param \Closure(T, int) $closure
     * @return self
     */
    public function transformFlat(Closure $closure): self
    {
        $r = $this->collection->flatMap($closure)->all();

        return new self(new Collection($r));
    }

    public function collapse(): self
    {
        $results = [];

        foreach ($this->collection->toArray() as $values) {
            if ($values instanceof self) {
                $results[] = $values->values()->toArray();
            }
        }

        return new self(new Collection(array_merge([], ...$results)));
    }

    /**
     * @return mixed
     */
    public function pipe(\Closure $closure)
    {
        return $closure($this);
    }

    /**
     * @param \Closure(T, int) $closure
     * @return self
     */
    public function each(Closure $closure): self
    {
        return new self($this->collection->each($closure));
    }

    public function count(): int
    {
        return $this->collection->count();
    }

    /**
     * @return self<self<T>>
     * @psalm-suppress InvalidArgument
     */
    public function chunk(int $size): self
    {
        if ($size <= 0) {
            return new self(new Collection([]));
        }

        $chunks = self::create([]);

        foreach (array_chunk($this->collection->all(), $size, true) as $chunk) {
            $chunks->add(self::create($chunk));
        }

        return $chunks;
    }

    /**
     * @param \Closure(self) $closure
     * @return self
     */
    public function tap(\Closure $closure): self
    {
        $closure(clone $this);

        return new self($this->collection);
    }

    /**
     * @param \Closure(T, int) $closure
     * @return ArrayHashMap
     */
    public function convertToHashMap(\Closure $closure): ArrayHashMap
    {
        return ArrayHashMap::create($this->collection->mapWithKeys($closure)->all());
    }

    /**
     * @param T $item
     */
    public function add($item): self
    {
        return new self($this->collection->add($item));
    }

    /**
     * @return array<int, T>
     */
    public function toArray(): array
    {
        return $this->collection->toArray();
    }

    public function toJson(): string
    {
        return $this->collection->toJson();
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->collection->toArray());
    }

    public function isEmpty(): bool
    {
        return $this->collection->isEmpty();
    }

    /**
     * @param \Closure(int, T) $closure
     */
    public function containsUsingFunction(Closure $callback): bool
    {
        return $this->collection->contains($callback);
    }

    /**
     * @param T $item
     */
    public function contains($item): bool
    {
        return $this->collection->contains($item);
    }

    /**
     * Returns the first element from the list.
     *
     * @return T
     * @throws FirstElementDoesNotExistsException
     */
    public function head()
    {
        if (!$this->has(0)) {
            throw new FirstElementDoesNotExistsException();
        }

        return $this->get(0);
    }

    public function keys(): self
    {
        return new self($this->collection->keys());
    }

    /**
     * @param T $replacement
     */
    public function splice(int $offset, ?int $length = null, $replacement = []): self
    {
        if ($replacement === []) {
            return new self($this->collection->splice($offset, $length));
        }

        return new self($this->collection->splice($offset, $length, $replacement));
    }

    /**
     * Groups the collection's items by the given closure. The closure should return an associative array containing a
     * single key / value pair, thus forming a new collection of grouped values
     *
     * @param callable $groupingClosure
     * @return ArrayList
     */
    public function mapToGroups(callable $groupingClosure): self
    {
        return new self($this->collection->mapToGroups($groupingClosure));
    }

    public function unique(): self
    {
        return new self($this->collection->unique());
    }

    /**
     * @param callable $expression
     * @return ArrayList
     */
    public function uniqueByExpression(callable $expression): self
    {
        return new self(new Collection($this->collection->unique($expression)));
    }


    public function flip(): ArrayList
    {
        return new self($this->collection->flip());
    }

    /**
     * @return T
     */
    public function pop()
    {
        return $this->collection->pop();
    }

    /**
     * Appends the given list onto the end of another list.
     *
     * @param ArrayList $list
     * @return self<T>
     */
    public function concat(ArrayList $list): self
    {
        return new self($this->collection->concat($list));
    }

    /**
     * @return T
     */
    public function last()
    {
        return $this->collection->last();
    }

    /**
     * @return T
     */
    public function first(): mixed
    {
        return $this->collection->first();
    }

    /**
     * @param T $item
     * @return self<T>
     */
    public function put(mixed $item, int $key): self
    {
        return new self($this->collection->put($key, $item));
    }

    /**
     * @param callable $callback
     * @return ArrayHashMap
     */
    public function groupByCallback(callable $callback): ArrayHashMap
    {
        $grouped = $this->collection->groupBy($callback);

        $convertedToArrayLists = $grouped->map(static fn(Collection $collection) => new self($collection));

        return new ArrayHashMap($convertedToArrayLists);
    }

    /**
     * @return ArrayList
     */
    public function reverse(): ArrayList
    {
        return new self($this->collection->reverse());
    }

    /**
     * The method compares the collection against another collection based on its values. This method will return the
     * values in the original collection that are not present in the given collection.
     *
     * @param ArrayList $anotherList
     * @return ArrayList
     */
    public function diff(ArrayList $anotherList): ArrayList
    {
        return new self($this->collection->diff($anotherList));
    }

    public function reduce(callable $callback): mixed
    {
        return $this->collection->reduce($callback);
    }

    public static function empty(): self
    {
        return self::create([]);
    }

    protected static function assertIsList(array $items): void
    {
        if (Arrays::isList($items) === false) {
            throw InvalidArrayListException::create();
        }
    }
}
