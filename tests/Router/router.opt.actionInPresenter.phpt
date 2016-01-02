<?php

/**
 * Test: Myiyk\SeoRouter\Router option 'actionInPresenter'
 */

include __DIR__ . '/../bootstrap.php';
include __DIR__ . '/router.php';

use Mockery as M;
use Myiyk\SeoRouter\Router;

class RouterOptionActionInPresenter extends RouterBaseTest
{
	function testEnabled()
	{
		$source = self::getSource('url',
			array(
				'Front:Homepage:default',
				array(
					'id' => 123,
				)
			));
		$router = new Router($source, array('actionInPresenter' => true));

		self::routeIn($router, '/url', 'Front:Homepage',
			array(
				'action' => 'default',
				'id' => 123,
				'test' => 'testvalue',
			), '/url?test=testvalue'
		);
	}

	function testEnabledWithActionInParameter()
	{
		$source = self::getSource('url',
			array(
				'Front:Homepage',
				array(
					'id' => 123,
					'action' => 'default',
				)
			));
		$router = new Router($source, array('actionInPresenter' => true));

		self::routeIn($router, '/url', 'Front:Homepage',
			array(
				'action' => 'default',
				'id' => 123,
				'test' => 'testvalue',
			), '/url?test=testvalue'
		);
	}

	function testDisabled()
	{
		$source = self::getSource('url',
			array(
				'Front:Homepage',
				array(
					'id' => 123,
					'action' => 'default',
				)
			));
		$router = new Router($source, array('actionInPresenter' => false));

		self::routeIn($router, '/url', 'Front:Homepage',
			array(
				'action' => 'default',
				'id' => 123,
				'test' => 'testvalue',
			), '/url?test=testvalue'
		);
	}

	function testDisabledWithoutAction()
	{
		$source = self::getSource('url',
			array(
				'Front:Homepage',
				array(
					'id' => 123,
				)
			));
		$router = new Router($source, array('actionInPresenter' => false));

		self::routeIn($router, '/url', 'Front:Homepage',
			array(
				'id' => 123,
				'test' => 'testvalue',
			), '/url?test=testvalue'
		);
	}

	function tearDown()
	{
		M::close();
	}
}

(new RouterOptionActionInPresenter())->run();
