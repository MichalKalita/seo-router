<?php

/**
 * Test: Myiyk\SeoRouter\Router invalid options
 */

include __DIR__ . '/../bootstrap.php';
include __DIR__ . '/router.php';


/**
 * Invalid options must throw exception
 */
\Tester\Assert::exception(function () {

	new \Myiyk\SeoRouter\Router(new Source(), array('invalidOption'));

}, '\Myiyk\SeoRouter\InvalidConfigurationException');
