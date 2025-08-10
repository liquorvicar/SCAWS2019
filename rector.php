<?php

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withAttributesSets()
    ->withPaths([
        __DIR__ . '/src/',
        __DIR__ . '/tests/',
    ])
;
