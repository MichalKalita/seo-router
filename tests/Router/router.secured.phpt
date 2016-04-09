<?php

/**
 * Test: Myiyk\SeoRouter\Router with Secure
 */

include __DIR__ . '/../bootstrap.php';

use Mockery as M;
use Myiyk\SeoRouter\Router;
use Tester\Assert;

class RouterSecured extends \Tester\TestCase
{
	function getSource($return)
	{
		$mock = M::mock('Myiyk\SeoRouter\ISource');
		$mock->shouldReceive('toUrl')
			->with(M::type('Myiyk\SeoRouter\Action'))->once()->andReturn($return);
		return $mock;
	}

	function testConfiguredInFlags()
	{
		$request = new \Nette\Application\Request('Front:Homepage', NULL,
			array(
				'action' => 'show',
				'id' => 123
			));

		$router = new Router($this->getSource('url'), array(), Router::SECURED);

		$httpUrl = new Nette\Http\Url("http://example.com");
		Assert::same('https://example.com/url', $router->constructUrl($request, $httpUrl));
	}

	function testConfiguredInOptions()
	{
		$request = new \Nette\Application\Request('Front:Homepage', NULL,
			array(
				'action' => 'show',
				'id' => 123
			));

		$router = new Router($this->getSource('url'), array('secured' => TRUE));

		$httpUrl = new Nette\Http\Url("http://example.com");
		Assert::same('https://example.com/url', $router->constructUrl($request, $httpUrl));
	}

	function tearDown()
	{
		M::close();
	}
}

(new RouterSecured())->run();
