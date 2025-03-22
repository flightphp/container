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
  /**
   * @var array<class-string, array{
   *   concrete: object|class-string|callable(ContainerInterface $container): object,
   *   isSingleton: bool
   * }>
   */
  private array $entries = [];

  /**
   * @template T of object
   * @param class-string<T> $id
   * @return T
   * @throws NotFoundExceptionInterface
   * @throws ContainerExceptionInterface
   */
  public function get(string $id): object
  {
    if ($this->has($id)) {
      [
        'concrete' => $concrete,
        'isSingleton' => $isSingleton
      ] = $this->entries[$id];

      if (is_callable($concrete)) {
        /** @var T */
        $object = $concrete($this);

        if ($isSingleton) {
          $this->singleton($id, $object);
        }

        return $object;
      }

      if (is_string($concrete)) {
        /** @var T */
        $object = $this->resolve($concrete);

        $this->singleton($id, $object);

        return $object;
      }

      if (is_object($concrete) && $isSingleton) {
        return $concrete;
      }
    }

    /** @var T */
    $object = $this->resolve($concrete ?? $id);

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
  public function set(string $id, $concrete): self
  {
    $this->entries[$id]['concrete'] = $concrete;
    $this->entries[$id]['isSingleton'] = false;

    return $this;
  }

  /**
   * @template T of object
   * @param class-string<T>|T $id
   */
  public function singleton($id): self
  {
    $fqcn = is_object($id) ? get_class($id) : $id;
    $concrete = func_num_args() === 2 ? func_get_arg(1) : $id;

    $this->entries[$fqcn]['concrete'] = $concrete;
    $this->entries[$fqcn]['isSingleton'] = true;

    return $this;
  }

  /**
   * @param class-string $id
   * @throws ContainerExceptionInterface
   * @throws NotFoundExceptionInterface
   */
  private function resolve(string $id): object
  {
    try {
      $reflectionClass = new ReflectionClass($id);

      if (!$reflectionClass->isInstantiable()) {
        throw new ContainerException("Class \"$id\" is not instantiable");
      }
    } catch (ReflectionException $reflectionException) {
      throw new NotFoundException("Class \"$id\" does not exist");
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
