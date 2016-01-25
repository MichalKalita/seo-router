<?php

/**
 * Test: Myiyk\SeoRouter\Extension without ISource in services
 */

use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$configurator = new Nette\Configurator();
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->addConfig(createFile('.neon', <<<NEON
application:
	scanDirs: false
	debugger: false

extensions:
	seoRouter: Myiyk\SeoRouter\Extension
NEON
));

$exception = Assert::throws(function () use ($configurator) {
	$configurator->createContainer();
}, 'Nette\DI\MissingServiceException');

Assert::contains('Myiyk\SeoRouter\ISource', $exception->getMessage());
