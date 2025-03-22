<?php

declare(strict_types=1);

namespace flight\tests\examples;

final class ExampleClassWithBuiltinConstructorParameter
{
  public int $parameter;

  public function __construct(int $parameter)
  {
    $this->parameter = $parameter;
  }
}
