<?php

/**
 * Test: Myiyk\SeoRouter\Extension with option, which is not on list
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

seoRouter:
	undefinedOption: value
NEON
));

$container = $configurator->createContainer();

$e = Assert::exception(function () use ($container) {
	$container->getService('seoRouter.router');
}, 'Myiyk\SeoRouter\InvalidConfigurationException');

Assert::same(\Myiyk\SeoRouter\InvalidConfigurationException::NOT_RECOGNIZED, $e->getCode());
