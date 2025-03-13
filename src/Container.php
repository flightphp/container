<?php

declare(strict_types=1);

namespace flight;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

final class Container implements ContainerInterface
{
  /** @var array<class-string, class-string|callable(ContainerInterface $container): object> */
  private array $entries = [];

  /**
   * @template T of object
   * @param class-string<T> $id
   * @return T
   * @throws NotFoundExceptionInterface
   */
  public function get(string $id): object
  {
    if ($this->has($id)) {
      $entry = $this->entries[$id];

      if (is_callable($entry)) {
        /** @var T */
        $concrete = $entry($this);

        return $concrete;
      }

      $id = $entry;
    }

    /** @var T */
    $object = $this->resolve($id);

    return $object;
  }

  /** @param class-string $id */
  public function has(string $id): bool
  {
    return isset($this->entries[$id]);
  }

  /**
   * @template T of object
   * @param class-string<T> $id
   * @param class-string<T>|callable(ContainerInterface $container): T $concrete
   */
  public function set(string $id, $concrete): void
  {
    $this->entries[$id] = $concrete;
  }

  /**
   * @param class-string $id
   * @throws ContainerExceptionInterface
   */
  private function resolve(string $id): object
  {
    try {
      $reflectionClass = new ReflectionClass($id);

      if (!$reflectionClass->isInstantiable()) {
        throw new ContainerException("Class \"$id\" is not instantiable");
      }
    } catch (ReflectionException $reflectionException) {
      throw new ContainerException("Class \"$id\" does not exist");
    }

    $constructor = $reflectionClass->getConstructor();

    if (!$constructor) {
      return new $id;
    }

    $parameters = $constructor->getParameters();

    if (!$parameters) {
      return new $id;
    }

    $dependencies = array_map(
      function (ReflectionParameter $reflectionParameter) use ($id) {
        $name = $reflectionParameter->getName();
        $type = $reflectionParameter->getType();

        if ($reflectionParameter->isOptional()) {
          return $reflectionParameter->getDefaultValue();
        }

        if (!$type) {
          throw new ContainerException("Failed to resolve class \"$id\" because param \"$name\" is missing a type hint");
        }

        if ($type instanceof ReflectionUnionType) {
          throw new ContainerException("Failed to resolve class \"$id\" because of union type for param \"$name\"");
        }

        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
          /** @var class-string */
          $typeName = $type->getName();

          return $this->get($typeName);
        }

        throw new ContainerException("Failed to resolve class \"$id\" because invalid param \"$name\"");
      },
      $parameters
    );

    return $reflectionClass->newInstanceArgs($dependencies);
  }
}
