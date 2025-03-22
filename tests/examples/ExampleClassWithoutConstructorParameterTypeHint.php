<?php

declare(strict_types=1);

namespace flight\tests\examples;

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
