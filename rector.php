<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
  ->withPaths([
    __DIR__ . '/src',
    __DIR__ . '/tests',
  ])
  ->withSkipPath(__DIR__ . '/vendor')
  ->withPhp74Sets()
  ->withFluentCallNewLine()
  ->withImportNames()
  ->withIndent(' ', 2)
  ->withParallel()
  ->withSets([
    SetList::BEHAT_ANNOTATIONS_TO_ATTRIBUTES,
    SetList::CODE_QUALITY,
    SetList::CODING_STYLE,
    SetList::DEAD_CODE,
    SetList::EARLY_RETURN,
    SetList::GMAGICK_TO_IMAGICK,
    SetList::INSTANCEOF,
    SetList::NAMING,
    SetList::PHP_POLYFILLS,
    SetList::PRIVATIZATION,
    SetList::RECTOR_PRESET,
    SetList::STRICT_BOOLEANS,
    SetList::TYPE_DECLARATION,
  ])
  ->withRootFiles()
  ->withSkip([
    EncapsedStringsToSprintfRector::class,
    WrapEncapsedVariableInCurlyBracesRector::class
  ]);
