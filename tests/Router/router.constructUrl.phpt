<?php
include __DIR__ . '/../bootstrap.php';

use Mockery as M;
use Tester\Assert;

class RouterConstructUrl extends \Tester\TestCase
{
	/** @var \Nette\Http\Url */
	protected $httpUrl;

	function setUp()
	{
		$this->httpUrl = new Nette\Http\Url("http://example.com");
	}

	function getSource(\Nette\Application\Request $request, $return)
	{
		$mock = M::mock('Myiyk\SeoRouter\ISource');
		$mock->shouldReceive('toUrl')
			->with($request)->once()->andReturn($return)
			->globally()->ordered();
		return $mock;
	}

	/**
	 * Do NOT inline this method, because it does not working
	 * @return \Myiyk\SeoRouter\ISource
	 */
	function getNeverCalledSource()
	{
		$mock = M::mock('Myiyk\SeoRouter\ISource');
		$mock->shouldReceive('toUrl')->never();
		return $mock;
	}

	function testNotFound()
	{
		$request = new \Nette\Application\Request('Front:Homepage', NULL,
			array(
				'action' => 'show',
				'id' => 123
			));

		$router = new \Myiyk\SeoRouter\Router($this->getSource($request, false));

		Assert::null($router->constructUrl($request, $this->httpUrl));
	}

	function testOneSource()
	{
		$request = new \Nette\Application\Request('Front:Homepage', NULL,
			array(
				'action' => 'show',
				'id' => 123
			));

		$router = new \Myiyk\SeoRouter\Router($this->getSource($request, 'url'));

		Assert::same('http://example.com/url', $router->constructUrl($request, $this->httpUrl));
	}

	function testMultipleSources()
	{
		$request = new \Nette\Application\Request('Front:Homepage', NULL,
			array(
				'action' => 'show',
				'id' => 123
			));

		// first source doest have url
		$router = new \Myiyk\SeoRouter\Router($this->getSource($request, false));
		$router->addSource($this->getSource($request, null));

		// second source have url
		$router->addSource($this->getSource($request, 'url'));

		// third source will never used
		$router->addSource($this->getNeverCalledSource());

		Assert::same('http://example.com/url', $router->constructUrl($request, $this->httpUrl));
	}

	function tearDown()
	{
		M::close();
	}
}

(new RouterConstructUrl())->run();
