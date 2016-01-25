<?php

/**
 * Test: Myiyk\SeoRouter\Extension with more ISources and have configuration
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
	source2:
		class: EmptySource
		autowired: false
	source3:
		class: EmptySource
		autowired: false

extensions:
	seoRouter: Myiyk\SeoRouter\Extension

seoRouter:
	sources:
		- @\EmptySource
		- @source2
		- @source3
NEON
));

$container = $configurator->createContainer();

Assert::type('Myiyk\SeoRouter\Router', $container->getService('seoRouter.router'));
