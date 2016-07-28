<?php

use Acme\ClockInCommand;
use Acme\ClockOutCommand;
use Acme\LogCommand;
use Acme\SetFolderCommand;
use Acme\PauseCommand;
use Acme\ResumeCommand;
use Acme\ResetCommand;
use Symfony\Component\Console\Application;

require 'vendor/autoload.php';

$cleared = json_decode('{"start": null,"end": null,"folder": null,"total": null,"logs": []}');

if (!file_exists("config.json")) {
    $config = fopen("config.json", 'w') or exit("Unable to write to config.");
    fwrite($config, json_encode($cleared, JSON_PRETTY_PRINT));
    fclose($config);
}

$config = json_decode(file_get_contents("config.json"));

$app = new Application('Track Time', '1.0');

$app->add(new ClockInCommand($config));
$app->add(new ClockOutCommand($config, $cleared));
$app->add(new LogCommand($config));
$app->add(new SetFolderCommand($config));
$app->add(new PauseCommand($config));
$app->add(new ResumeCommand($config));
$app->add(new ResetCommand($config, $cleared));

$app->run();