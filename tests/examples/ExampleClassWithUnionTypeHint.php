<?php

declare(strict_types=1);

namespace flight\tests\examples;

use DateTime;
use DateTimeImmutable;

final class ExampleClassWithUnionTypeHint
{
  public function __construct(DateTimeImmutable|DateTime $dateTime)
  {
    // No parameters
  }
}
