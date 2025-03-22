<?php

declare(strict_types=1);

namespace flight\tests;

use flight\Container;
use flight\tests\examples\ExampleClassWithRandomDefaultProperty;
use flight\tests\examples\ExampleImplementation;
use flight\tests\examples\ExampleInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;

final class ContainerSetTest extends TestCase
{
  public function test_can_set_a_non_singleton_class_from_a_fqcn(): void
  {
    $container = new Container;

    $container->set(
      ExampleClassWithRandomDefaultProperty::class,
      ExampleClassWithRandomDefaultProperty::class
    );

    $exampleClassWithRandomDefaultProperty = $container->get(ExampleClassWithRandomDefaultProperty::class);
    $exampleClassWithRandomDefaultProperty2 = $container->get(ExampleClassWithRandomDefaultProperty::class);

    self::assertInstanceOf(
      ExampleClassWithRandomDefaultProperty::class,
      $exampleClassWithRandomDefaultProperty
    );

    self::assertNotEquals(
      $exampleClassWithRandomDefaultProperty,
      $exampleClassWithRandomDefaultProperty2
    );
  }

  public function test_can_set_a_non_singleton_class_from_a_callable(): void
  {
    $container = new Container;

    $container->set(
      ExampleClassWithRandomDefaultProperty::class,
      static fn(): ExampleClassWithRandomDefaultProperty => new ExampleClassWithRandomDefaultProperty
    );

    $exampleClassWithRandomDefaultProperty = $container->get(ExampleClassWithRandomDefaultProperty::class);
    $exampleClassWithRandomDefaultProperty2 = $container->get(ExampleClassWithRandomDefaultProperty::class);

    self::assertInstanceOf(ExampleClassWithRandomDefaultProperty::class, $exampleClassWithRandomDefaultProperty);
    self::assertNotEquals($exampleClassWithRandomDefaultProperty, $exampleClassWithRandomDefaultProperty2);
  }

  public function test_it_receives_container_as_callable_parameter(): void
  {
    $container = new Container;

    $container
      ->set(
        stdClass::class,
        function (ContainerInterface $containerInterface) use ($container): object {
          self::assertSame($container, $containerInterface);

          return new stdClass;
        }
      )
      ->get(stdClass::class);
  }

  public function test_can_set_a_non_singleton_class_from_an_object(): void
  {
    $container = new Container;
    $exampleClassWithRandomDefaultProperty = new ExampleClassWithRandomDefaultProperty;

    $exampleClassWithRandomDefaultProperty2 = $container
      ->set(
        ExampleClassWithRandomDefaultProperty::class,
        $exampleClassWithRandomDefaultProperty
      )
      ->get(ExampleClassWithRandomDefaultProperty::class);

    self::assertInstanceOf(
      ExampleClassWithRandomDefaultProperty::class,
      $exampleClassWithRandomDefaultProperty2
    );

    self::assertNotEquals(
      $exampleClassWithRandomDefaultProperty,
      $exampleClassWithRandomDefaultProperty2
    );
  }

  public function test_can_set_a_non_singleton_implementation_from_a_fqcn(): void
  {
    $container = new Container;

    $container->set(
      ExampleInterface::class,
      ExampleImplementation::class
    );

    $exampleImplementation = $container->get(ExampleInterface::class);
    $exampleImplementation2 = $container->get(ExampleInterface::class);

    self::assertInstanceOf(
      ExampleImplementation::class,
      $exampleImplementation
    );

    self::assertEquals($exampleImplementation, $exampleImplementation2);
    self::assertNotSame($exampleImplementation, $exampleImplementation2);
  }

  public function test_can_set_a_non_singleton_implementation_from_a_callable(): void
  {
    $container = new Container;

    $container->set(
      ExampleInterface::class,
      static fn(): ExampleImplementation => new ExampleImplementation
    );

    $exampleImplementation = $container->get(ExampleInterface::class);
    $exampleImplementation2 = $container->get(ExampleInterface::class);

    self::assertInstanceOf(ExampleImplementation::class, $exampleImplementation);
    self::assertEquals($exampleImplementation, $exampleImplementation2);
    self::assertNotSame($exampleImplementation, $exampleImplementation2);
  }

  public function test_can_set_a_non_singleton_implementation_from_an_object(): void
  {
    $container = new Container;
    $exampleImplementation = new ExampleImplementation;

    $exampleImplementation2 = $container
      ->set(ExampleInterface::class, $exampleImplementation)
      ->get(ExampleInterface::class);

    self::assertInstanceOf(ExampleImplementation::class, $exampleImplementation2);
    self::assertEquals($exampleImplementation, $exampleImplementation2);
    self::assertNotSame($exampleImplementation, $exampleImplementation2);
  }
}
