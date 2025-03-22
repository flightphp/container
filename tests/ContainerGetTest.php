<?php

declare(strict_types=1);

use flight\Container;
use flight\tests\examples\ExampleClassWithAnNonBuiltinConstructorParameter;
use flight\tests\examples\ExampleClassWithAnOptionalConstructorParameter;
use flight\tests\examples\ExampleClassWithoutConstructorParameters;
use flight\tests\examples\ExampleClassWithRandomDefaultProperty;
use PHPUnit\Framework\TestCase;

final class ContainerGetTest extends TestCase
{
  public function test_it_can_get_a_class(): void
  {
    $container = new Container;

    self::assertInstanceOf(stdClass::class, $container->get(stdClass::class));
  }

  public function test_can_get_a_class_without_constructor_parameters(): void
  {
    $container = new Container;

    self::assertInstanceOf(
      ExampleClassWithoutConstructorParameters::class,
      $container->get(ExampleClassWithoutConstructorParameters::class)
    );
  }

  public function test_it_can_get_a_class_with_a_constructor_with_default_values(): void
  {
    $container = new Container;

    self::assertInstanceOf(
      ExampleClassWithRandomDefaultProperty::class,
      $container->get(ExampleClassWithRandomDefaultProperty::class)
    );
  }

  public function test_can_get_a_class_with_a_non_builtin_constructor_parameter(): void
  {
    $container = new Container;

    self::assertInstanceOf(
      ExampleClassWithAnNonBuiltinConstructorParameter::class,
      $container->get(ExampleClassWithAnNonBuiltinConstructorParameter::class)
    );
  }

  public function test_it_get_optional_non_builtin_classes_constructor_parameters(): void
  {
    $container = new Container;

    self::assertInstanceOf(
      ExampleClassWithAnOptionalConstructorParameter::class,
      $container->get(ExampleClassWithAnOptionalConstructorParameter::class)
    );
  }
}
