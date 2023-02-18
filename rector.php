<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        //__DIR__ . '/config',
        //__DIR__ . '/public',
        __DIR__ . '/src',
    ]);

    // register a single rule
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);

    $rectorConfig->sets([
        //Rector\Set\ValueObject\LevelSetList::UP_TO_PHP_82,       // ✅
        //Rector\Symfony\Set\SymfonySetList::SYMFONY_62            // ⚠️ Sf6.2 bloqué par la version maison de EasyAdmin
        //Rector\Symfony\Set\SymfonySetList::SYMFONY_CODE_QUALITY  // ✅
        //Rector\Symfony\Set\SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES // TODO
    ]);
};
