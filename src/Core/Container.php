<?php
namespace Paw\Core;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

class Container implements ContainerInterface
{
    private array $entries = [];

    public function set(string $id, $resolver): void
    {
        $this->entries[$id] = $resolver;
    }

    public function get(string $id)
    {
        if (! $this->has($id)) {
            throw new class("No hay entry para {$id}") extends \Exception implements NotFoundExceptionInterface {};
        }
        $entry = $this->entries[$id];
        if (is_callable($entry)) {
            $object = $entry($this);
            $this->entries[$id] = $object;
            return $object;
        }
        return $entry;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->entries);
    }
}