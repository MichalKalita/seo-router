<?php

/**
 * Test: Myiyk\SeoRouter\Extension with more ISources and have NOT configuration
 */

use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$configurator = new Nette\Configurator();
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->addConfig(createFile('.neon', <<<NEON
application:
	scanDirs: false
	debugger: false

services:
	- EmptySource
	-
		class: EmptySource
		autowired: true
	-
		class: EmptySource
		autowired: false

extensions:
	seoRouter: Myiyk\SeoRouter\Extension
NEON
));

$e = Assert::exception(function () use ($configurator) {
	$configurator->createContainer();
}, 'Myiyk\SeoRouter\InvalidConfigurationException');

Assert::same(\Myiyk\SeoRouter\InvalidConfigurationException::MISSING_SOURCES, $e->getCode());
