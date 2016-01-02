<?php

/**
 * Test: Myiyk\SeoRouter\Router invalid options
 */

include __DIR__ . '/../bootstrap.php';
include __DIR__ . '/router.php';

use Mockery as M;
use Myiyk\SeoRouter\Router;

class RouterOptionInvalid extends RouterBaseTest
{
	/**
	 * @throws \Myiyk\SeoRouter\InvalidOptionsException
	 */
	function testInvalidOptions()
	{
		$source = self::getSource('', array(''));
		new Router($source, array('invalidOption'));
	}

	function tearDown()
	{
		M::close();
	}
}

(new RouterOptionInvalid())->run();
