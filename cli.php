#!/usr/bin/env php
<?php
//cli.php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;

$console = new Application('Symfony Console demo for Zdroják.cz', '4.5.6');

$console->addCommands(
    [
        new App\Command\WebshotSimpleCommand(),
        new App\Command\WebshotProcessBuilderCommand(),
        new App\Command\WebshotMultipleCommand(),
    ]
);
$console->run();
