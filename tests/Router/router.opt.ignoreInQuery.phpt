<?php

/**
 * Test: Myiyk\SeoRouter\Router option 'ignoreInQuery'
 */

include __DIR__ . '/../bootstrap.php';
include __DIR__ . '/router.php';

use Mockery as M;
use Myiyk\SeoRouter\Router;

class RouterOptionIgnoreInQuery extends RouterBaseTest
{
	function testDefault()
	{
		$source = self::getSource('url',
			array(
				'Front:Homepage',
				array(
					'action' => 'default',
					'id' => 123,
				)
			));
		$router = new Router($source);

		self::routeIn($router, '/url?action=queryaction&id=queryid', 'Front:Homepage',
			array(
				'action' => 'default',
				'id' => 123,
				'test' => 'testvalue',
			), '/url?test=testvalue'
		);
	}

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
		$router = new Router($source, array('ignoreInQuery' => array()));

		self::routeIn($router, '/url?action=queryaction&id=queryid', 'Front:Homepage',
			array(
				'action' => 'default',
				'id' => 123,
				'test' => 'testvalue',
			), '/url?action=default&test=testvalue&id=123'
		);

		$router = new Router($source, array('ignoreInQuery' => null));

		self::routeIn($router, '/url?action=queryaction&id=queryid', 'Front:Homepage',
			array(
				'action' => 'default',
				'id' => 123,
				'test' => 'testvalue',
			), '/url?action=default&test=testvalue&id=123'
		);
	}

	function testIgnoreAll()
	{
		$source = self::getSource('url',
			array(
				'Front:Homepage',
				array(
					'action' => 'default',
					'id' => 123,
				)
			));
		$router = new Router($source, array('ignoreInQuery' => array('action', 'presenter', 'test', 'id')));

		self::routeIn($router, '/url?action=queryaction&id=queryid', 'Front:Homepage',
			array(
				'action' => 'default',
				'id' => 123,
				'test' => 'testvalue',
			), '/url'
		);
	}

	function tearDown()
	{
		M::close();
	}
}

(new RouterOptionIgnoreInQuery())->run();
