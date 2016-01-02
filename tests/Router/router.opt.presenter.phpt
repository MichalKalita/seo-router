<?php

/**
 * Test: Myiyk\SeoRouter\Router option 'presenter'
 */

include __DIR__ . '/../bootstrap.php';
include __DIR__ . '/router.php';

use Mockery as M;
use Myiyk\SeoRouter\Router;

class RouterOptionPresenter extends RouterBaseTest
{
	function testPresenterInAll()
	{
		$source = self::getSource('url',
			array(
				'Source',
				array(
					'action' => 'default',
					'id' => 123,
				)
			));
		$router = new Router($source, array('presenter' => 'Default'));

		self::routeIn($router, '/url?presenter=Query', 'Source',
			array(
				'action' => 'default',
				'id' => 123,
				'test' => 'testvalue',
			), '/url?test=testvalue'
		);
	}

	function testPresenterInQueryAndDefault()
	{
		$source = self::getSource('url',
			array(
				'',
				array(
					'action' => 'default',
					'id' => 123,
				)
			));
		$router = new Router($source, array('presenter' => 'Default'));

		self::routeIn($router, '/url?presenter=Query', 'Query',
			array(
				'action' => 'default',
				'id' => 123,
				'test' => 'testvalue',
			), '/url?test=testvalue'
		);
	}

	function testPresenterInDefault()
	{
		$source = self::getSource('url',
			array(
				'',
				array(
					'action' => 'default',
					'id' => 123,
				)
			));
		$router = new Router($source, array('presenter' => 'Default'));

		self::routeIn($router, '/url?presenter', 'Default',
			array(
				'action' => 'default',
				'id' => 123,
				'test' => 'testvalue',
			), '/url?test=testvalue'
		);
	}

	function testPresenterNotSet()
	{
		$source = self::getSource('url',
			array(
				'',
				array(
					'action' => 'default',
					'id' => 123,
				)
			));
		$router = new Router($source, array('presenter' => NULL));

		self::routeIn($router, '/url?presenter', NULL);
	}

	function tearDown()
	{
		M::close();
	}
}

(new RouterOptionPresenter())->run();
