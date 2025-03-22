<?php

declare(strict_types=1);

namespace flight\tests\examples;

final class ExampleClassWithAnOptionalConstructorParameter
{
  public string $parameter;

  public function __construct(string $parameter = 'default')
  {
    $this->parameter = $parameter;
  }
}
