<?php

declare(strict_types=1);

use flight\Container;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;

/** @covers flight\Container */
final class ContainerTest extends TestCase
{
  public function test_it_can_resolve_a_class(): void
  {
    $container = new Container;

    self::assertInstanceOf(stdClass::class, $container->get(stdClass::class));
  }

  public function test_it_can_resolve_a_class_with_a_constructor_with_default_values(): void
  {
    $container = new Container;

    self::assertInstanceOf(
      DateTimeImmutable::class,
      @$container->get(DateTimeImmutable::class)
    );
  }

  public function test_it_throws_an_exception_when_the_class_does_not_exist(): void
  {
    $container = new Container;

    $this->expectException(ContainerExceptionInterface::class);

    $container->get('NonExistentClass'); // @phpstan-ignore-line
  }

  public function test_can_set_a_class(): void
  {
    $container = new Container;

    $container->set(
      DateTimeImmutable::class,
      static fn(): DateTimeImmutable => new DateTimeImmutable('2025-03-09 07:29:00')
    );

    $dateTimeImmutable = $container->get(DateTimeImmutable::class);

    self::assertInstanceOf(DateTimeImmutable::class, $dateTimeImmutable);
    self::assertSame('2025-03-09 07:29:00', $dateTimeImmutable->format('Y-m-d H:i:s'));
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

  public function test_can_set_an_implementation(): void
  {
    $container = new Container;

    $container->set(
      ExampleInterface::class,
      ExampleImplementation::class
    );

    self::assertInstanceOf(
      ExampleImplementation::class,
      $container->get(ExampleInterface::class)
    );
  }

  public function test_can_get_a_class_without_constructor_parameters(): void
  {
    $container = new Container;

    self::assertInstanceOf(
      ExampleClassWithoutConstructorParameters::class,
      $container->get(ExampleClassWithoutConstructorParameters::class)
    );
  }

  public function test_it_throws_an_exception_when_a_constructor_parameter_does_not_have_type_hint(): void
  {
    self::expectException(ContainerExceptionInterface::class);

    self::expectExceptionMessage(
      'Failed to resolve class "ExampleClassWithoutConstructorParameterTypeHint" because param "parameter" is missing'
    );

    $container = new Container;
    $container->get(ExampleClassWithoutConstructorParameterTypeHint::class);
  }

  public function test_it_throws_an_exception_when_the_class_has_built_in_constructor_parameters(): void
  {
    self::expectException(ContainerExceptionInterface::class);

    self::expectExceptionMessage(
      'Failed to resolve class "ExampleClassWithBuiltinConstructorParameter" because invalid param "parameter"'
    );

    $container = new Container;
    $container->get(ExampleClassWithBuiltinConstructorParameter::class);
  }

  public function test_can_get_a_class_with_a_non_builtin_constructor_parameter(): void
  {
    $container = new Container;

    self::assertInstanceOf(
      ExampleClassWithAnNonBuiltinConstructorParameter::class,
      @$container->get(ExampleClassWithAnNonBuiltinConstructorParameter::class)
    );
  }

  public function test_it_throws_an_exception_when_the_class_has_a_union_type_hint(): void
  {
    if (PHP_VERSION < '8.0') {
      self::markTestSkipped('Union types are only available in PHP 8.0 and later');
    }

    self::expectException(ContainerExceptionInterface::class);

    self::expectExceptionMessage(
      'Failed to resolve class "ExampleClassWithUnionTypeHint" because of union type for param "dateTime"'
    );

    $container = new Container;
    $container->get(ExampleClassWithUnionTypeHint::class);
  }

  public function test_it_resolves_optional_non_builtin_classes_constructor_parameters(): void
  {
    $container = new Container;

    self::assertInstanceOf(
      ExampleClassWithAnOptionalConstructorParameter::class,
      $container->get(ExampleClassWithAnOptionalConstructorParameter::class)
    );
  }

  /** @return array{0: class-string}[] */
  public static function nonInstantiableClassStringsDataProvider(): array
  {
    return [
      [ExampleAbstractClass::class],
      [ExampleTrait::class],
      [ExampleInterface::class],
    ];
  }
}

abstract class ExampleAbstractClass {}

trait ExampleTrait {}

interface ExampleInterface {}

final class ExampleImplementation implements ExampleInterface {}

final class ExampleClassWithoutConstructorParameters
{
  public function __construct()
  {
    // No parameters
  }
}

final class ExampleClassWithoutConstructorParameterTypeHint
{
  /** @var mixed */
  public $parameter;

  /** @param mixed $parameter */
  public function __construct($parameter)
  {
    $this->parameter = $parameter;
  }
}

final class ExampleClassWithBuiltinConstructorParameter
{
  public int $parameter;

  public function __construct(int $parameter)
  {
    $this->parameter = $parameter;
  }
}

final class ExampleClassWithAnNonBuiltinConstructorParameter
{
  public DateTimeImmutable $dateTime;

  public function __construct(DateTimeImmutable $dateTimeImmutable)
  {
    $this->dateTime = $dateTimeImmutable;
  }
}

final class ExampleClassWithAnOptionalConstructorParameter
{
  public string $parameter;

  public function __construct(string $parameter = 'default')
  {
    $this->parameter = $parameter;
  }
}

if (PHP_VERSION >= '8.0') {
  require_once __DIR__ . '/ExampleClassWithUnionTypeHint.php';
}
