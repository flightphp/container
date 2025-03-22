<?php

declare(strict_types=1);

namespace flight\tests\examples;

use DateTimeImmutable;

final class ExampleClassWithAnNonBuiltinConstructorParameter
{
  public DateTimeImmutable $dateTime;

  public function __construct(DateTimeImmutable $dateTimeImmutable)
  {
    $this->dateTime = $dateTimeImmutable;
  }
}
