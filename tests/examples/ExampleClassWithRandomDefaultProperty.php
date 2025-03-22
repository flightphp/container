<?php

declare(strict_types=1);

namespace flight\tests\examples;

final class ExampleClassWithRandomDefaultProperty
{
  public string $parameter;

  public function __construct()
  {
    $this->parameter = uniqid();
  }
}
