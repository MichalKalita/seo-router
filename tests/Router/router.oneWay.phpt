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

	function testOneSourceNoResult()
	{
		$request = new \Nette\Application\Request('Front:Homepage', NULL,
			array(
				'action' => 'show',
				'id' => 123
			));

		$router = new Router($this->getSource($request), Router::ONE_WAY);

		$httpUrl = new Nette\Http\Url("http://example.com");
		Assert::same(NULL, $router->constructUrl($request, $httpUrl));
	}

	function tearDown()
	{
		M::close();
	}
}

(new RouterOneWay())->run();
