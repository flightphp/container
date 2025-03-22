<?php

declare(strict_types=1);

namespace flight\tests;

use DateTimeImmutable;
use flight\Container;
use flight\tests\examples\ExampleImplementation;
use flight\tests\examples\ExampleInterface;
use PHPUnit\Framework\TestCase;

final class ContainerSingletonTest extends TestCase
{
  public function test_can_set_a_singleton_from_fqcn(): void
  {
    $container = new Container;

    $container->singleton(DateTimeImmutable::class);

    $dateTimeImmutable = $container->get(DateTimeImmutable::class);
    $dateTimeImmutable2 = $container->get(DateTimeImmutable::class);

    self::assertSame($dateTimeImmutable, $dateTimeImmutable2);
  }

  public function test_can_set_a_singleton_from_object(): void
  {
    $container = new Container;

    $dateTimeImmutable = new DateTimeImmutable('2025-03-22 16:13:00');

    $dateTimeImmutable2 = $container
      ->singleton($dateTimeImmutable)
      ->get(DateTimeImmutable::class);

    self::assertSame($dateTimeImmutable, $dateTimeImmutable2);
  }

  public function test_can_set_an_implementation_as_singleton_from_a_fqcn(): void
  {
    $container = new Container;

    $container->singleton(
      ExampleInterface::class,
      ExampleImplementation::class
    );

    $exampleImplementation = $container->get(ExampleInterface::class);
    $exampleImplementation2 = $container->get(ExampleInterface::class);

    self::assertSame($exampleImplementation, $exampleImplementation2);
  }

  public function test_can_set_an_implementation_as_singleton_from_an_object(): void
  {
    $container = new Container;

    $exampleImplementation = new ExampleImplementation;

    $exampleImplementation2 = $container
      ->singleton(ExampleInterface::class, $exampleImplementation)
      ->get(ExampleInterface::class);

    self::assertSame($exampleImplementation, $exampleImplementation2);
  }
}
