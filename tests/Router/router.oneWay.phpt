<?php

/**
 * Test: Myiyk\SeoRouter\Router with OneWay
 */

include __DIR__ . '/../bootstrap.php';

use Mockery as M;
use Myiyk\SeoRouter\Router;
use Tester\Assert;

class RouterOneWay extends \Tester\TestCase
{
	function getSource($request)
	{
		$mock = M::mock('Myiyk\SeoRouter\ISource');
		$mock->shouldReceive('toUrl')
			->with($request)->never();
		return $mock;
	}

	function testConfiguredInFlags()
	{
		$request = new \Nette\Application\Request('Front:Homepage', NULL,
			array(
				'action' => 'show',
				'id' => 123
			));

		$router = new Router($this->getSource($request), array(), Router::ONE_WAY);

		$httpUrl = new Nette\Http\Url("http://example.com");
		Assert::same(NULL, $router->constructUrl($request, $httpUrl));
	}

	function testConfiguredInOptions()
	{
		$request = new \Nette\Application\Request('Front:Homepage', NULL,
			array(
				'action' => 'show',
				'id' => 123
			));

		$router = new Router($this->getSource($request), array('oneWay' => TRUE));

		$httpUrl = new Nette\Http\Url("http://example.com");
		Assert::same(NULL, $router->constructUrl($request, $httpUrl));
	}

	function tearDown()
	{
		M::close();
	}
}

(new RouterOneWay())->run();
