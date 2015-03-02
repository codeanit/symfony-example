#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use BackendBundle\Command\CronCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new CronCommand);
$application->run();