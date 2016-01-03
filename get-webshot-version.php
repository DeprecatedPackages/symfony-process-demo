<?php
// show-webshot-version.php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Process\Process;

// Vytvoříme instanci Process
$process = new Process('./node_modules/.bin/webshot --version');
// Proces spustíme
$process->run();
// Po dokončení vypíšeme výstup
echo 'Verze je: ' . $process->getOutput();
