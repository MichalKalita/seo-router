<?php

/**
 * Test: Myiyk\SeoRouter\Router option 'ignoreUrl'
 */

include __DIR__ . '/../bootstrap.php';
include __DIR__ . '/router.php';

use Mockery as M;
use Myiyk\SeoRouter\Router;

class RouterOptionIgnoreUrl extends RouterBaseTest
{
	function testEmpty()
	{
		$source = self::getSource('url',
			array(
				'Front:Homepage',
				array(
					'action' => 'default',
					'id' => 123,
				)
			));
		$router = new Router($source, array('ignoreUrl' => array()));

		self::routeIn($router, '/url', 'Front:Homepage',
			array(
				'action' => 'default',
				'id' => 123,
				'test' => 'testvalue',
			), '/url?test=testvalue'
		);

		$router = new Router($source, array('ignoreUrl' => NULL));

		self::routeIn($router, '/url', 'Front:Homepage',
			array(
				'action' => 'default',
				'id' => 123,
				'test' => 'testvalue',
			), '/url?test=testvalue'
		);
	}

	function testIgnore()
	{
		$source = self::getSource('url',
			array(
				'Front:Homepage',
				array(
					'action' => 'default',
					'id' => 123,
				)
			));
		$router = new Router($source, array('ignoreUrl' => array('url')));

		self::routeIn($router, '/url', null);
	}

	function tearDown()
	{
		M::close();
	}
}

(new RouterOptionIgnoreUrl())->run();
