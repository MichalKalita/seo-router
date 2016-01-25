<?php

/**
 * Test: Myiyk\SeoRouter\Extension with one ISource in services
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

extensions:
	seoRouter: Myiyk\SeoRouter\Extension
NEON
));

$container = $configurator->createContainer();

Assert::type('Myiyk\SeoRouter\Router', $container->getService('seoRouter.router'));
