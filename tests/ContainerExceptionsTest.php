<?php

declare(strict_types=1);

namespace flight\tests;

use flight\Container;
use flight\tests\examples\ExampleAbstractClass;
use flight\tests\examples\ExampleClassWithBuiltinConstructorParameter;
use flight\tests\examples\ExampleClassWithoutConstructorParameterTypeHint;
use flight\tests\examples\ExampleClassWithUnionTypeHint;
use flight\tests\examples\ExampleInterface;
use flight\tests\examples\ExampleTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final class ContainerExceptionsTest extends TestCase
{
  public function test_it_throws_an_exception_when_the_class_does_not_exist(): void
  {
    $container = new Container;

    self::expectException(NotFoundExceptionInterface::class);
    self::expectExceptionMessage('Class "NonExistentClass" does not exist');

    $container->get('NonExistentClass'); // @phpstan-ignore-line
  }

  /**
   * @dataProvider nonInstantiableClassStringsDataProvider
   * @param class-string $class
   */
  public function test_it_throws_an_exception_when_the_class_is_not_instantiable(
    string $class
  ): void {
    self::expectException(ContainerExceptionInterface::class);
    self::expectExceptionMessage("Class \"$class\" is not instantiable");

    $container = new Container;
    $container->get($class);
  }

  public function test_it_throws_an_exception_when_a_constructor_parameter_does_not_have_type_hint(): void
  {
    self::expectException(ContainerExceptionInterface::class);

    self::expectExceptionMessage(
      'Failed to resolve class "'
        . ExampleClassWithoutConstructorParameterTypeHint::class
        . '" because param "parameter" is missing'
    );

    $container = new Container;
    $container->get(ExampleClassWithoutConstructorParameterTypeHint::class);
  }

  public function test_it_throws_an_exception_when_the_class_has_built_in_constructor_parameters(): void
  {
    self::expectException(ContainerExceptionInterface::class);

    self::expectExceptionMessage(
      'Failed to resolve class "'
        . ExampleClassWithBuiltinConstructorParameter::class
        . '" because invalid param "parameter"'
    );

    $container = new Container;
    $container->get(ExampleClassWithBuiltinConstructorParameter::class);
  }

  public function test_it_throws_an_exception_when_the_class_has_a_union_type_hint(): void
  {
    if (PHP_VERSION < '8.0') {
      self::markTestSkipped('Union types are only available in PHP 8.0 and later');
    }

    self::expectException(ContainerExceptionInterface::class);

    self::expectExceptionMessage(
      'Failed to resolve class "'
        . ExampleClassWithUnionTypeHint::class
        . '" because of union type for param "dateTime"'
    );

    $container = new Container;
    $container->get(ExampleClassWithUnionTypeHint::class);
  }

  /** @return array{0: class-string}[] */
  public static function nonInstantiableClassStringsDataProvider(): array
  {
    return [
      [ExampleAbstractClass::class],
      [ExampleInterface::class],
      [ExampleTrait::class]
    ];
  }
}

if (PHP_VERSION >= '8.0') {
  require_once __DIR__ . '/examples/ExampleClassWithUnionTypeHint.php';
}
