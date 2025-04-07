<?php

declare(strict_types=1);

namespace flight;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

if (!class_exists(Container::class)) {
  final class Container implements ContainerInterface
  {
    public static function getInstance(): self
    {
      return new self;
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @return T
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function get(string $id): object
    {
      if (!$this->has($id)) {
        throw new NotFoundException;
      }

      return new $id;
    }

    /** @param class-string $id */
    public function has(string $id): bool
    {
      return false;
    }

    /**
     * @template T of object
     * @param class-string<T>|T $id
     * @param null|class-string<T>|T|callable(ContainerInterface $container): T $concrete
     */
    public function singleton($id, $concrete = null): self
    {
      return $this;
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @param class-string<T>|T|callable(ContainerInterface $container): T $concrete
     */
    public function set(string $id, $concrete): self
    {
      return $this;
    }
  }
}
