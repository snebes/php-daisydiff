<?php

namespace DaisyDiff\Xml;

use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * Attribute collection.
 */
class AttributeBag implements IteratorAggregate, Countable
{
    /** @var array */
    private $attributes = [];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Returns the attributes.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->attributes;
    }

    /**
     * Returns the attribute keys.
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->attributes);
    }

    /**
     * Replace the current attributes with a new set.
     *
     * @param array $attributes
     */
    public function replace(array $attributes = []): void
    {
        $this->attributes = $attributes;
    }

    /**
     * Add attributes.
     *
     * @param array $attributes
     */
    public function add(array $attributes = []): void
    {
        $this->attributes = array_replace($this->attributes, $attributes);
    }

    /**
     * Returns an attribute by name.
     *
     * @param  string $key
     * @param  string $default
     * @return string
     */
    public function get(string $key, string $default = ''): string
    {
        return array_key_exists($key, $this->attributes)? $this->attributes[$key] : $default;
    }

    /**
     * Sets an attribute by name.
     *
     * @param string $key
     * @param string $value
     */
    public function set(string $key, string $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Returns true if the attribute is defined.
     *
     * @param  $key
     * @return bool
     */
    public function has($key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Removes an attribute.
     *
     * @param string $key
     */
    public function remove(string $key): void
    {
        unset($this->attributes[$key]);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->attributes);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->attributes);
    }
}
