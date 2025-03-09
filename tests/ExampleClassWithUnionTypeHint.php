<?php

declare(strict_types=1);

final class ExampleClassWithUnionTypeHint
{
  public function __construct(DateTimeImmutable|DateTime $dateTime)
  {
    // No parameters
  }
}
