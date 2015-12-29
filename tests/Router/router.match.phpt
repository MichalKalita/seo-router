<?php
include __DIR__ . '/../bootstrap.php';

use Mockery as M;
use Tester\Assert;

class RouterMatch extends \Tester\TestCase
{
	function getSource()
	{
		$mock = M::mock('Myiyk\SeoRouter\ISource');
		$mock->shouldReceive('toAction')->with('url')->once()->andReturn(false)->globally()->ordered();
		return $mock;
	}

	function testOneSourceNoResult()
	{
		$router = new \Myiyk\SeoRouter\Router($this->getSource());

		$url = new Nette\Http\UrlScript("http://example.com/url");

		$httpRequest = new Nette\Http\Request($url);

		Assert::null($router->match($httpRequest));
	}

	function testOneSource()
	{
		$mock = M::mock('Myiyk\SeoRouter\ISource');
		$mock->shouldReceive('toAction')->with('url')->once()->andReturn(
			new \Nette\Application\Request('Presenter', NULL, array('action' => 'default', 'id' => 123))
		);

		$router = new \Myiyk\SeoRouter\Router($mock);

		$url = new Nette\Http\UrlScript("http://example.com/url");

		$httpRequest = new Nette\Http\Request($url);

		$result = $router->match($httpRequest);
		Assert::type('Nette\Application\Request', $result);
		Assert::same('Presenter', $result->getPresenterName());
		Assert::same('default', $result->getParameter('action'));
		Assert::same(123, $result->getParameter('id'));
	}

	/**
	 * Testovani se tremi zdroji
	 */
	function testMultipleSources()
	{
		$router = new \Myiyk\SeoRouter\Router($this->getSource());
		$router->addSource($this->getSource());
		$router->addSource($this->getSource());

		$url = new Nette\Http\UrlScript("http://example.com/url");

		$httpRequest = new Nette\Http\Request($url);

		Assert::null($router->match($httpRequest));
	}

	function tearDown()
	{
		M::close();
	}
}

(new RouterMatch())->run();
