<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

# $configurator->setDebugMode(true);
# $configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');

$container = $configurator->createContainer();

# Enable log4php
Logger::configure(__DIR__ . '/config/config.ini');

return $container;
