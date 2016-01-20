<?php
include __DIR__ . '/../bootstrap.php';
include __DIR__ . '/router.php';

use Mockery as M;

class RouterConstructUrl extends RouterBaseTest
{
	/** @var \Nette\Http\Url */
	protected $httpUrl;

	function setUp()
	{
		$this->httpUrl = new Nette\Http\Url("http://example.com");
	}

	function testNotFound()
	{
		$router = new \Myiyk\SeoRouter\Router(self::getSource('',
			array('Front:Homepage',
				array(
					'action' => 'show',
					'id' => 123,
				)
			)
		));

		$this->routeIn($router, 'url', 'Front:Homepage', array(
			'action' => 'show',
			'test' => 'testvalue',
			'id' => 123,
		));
	}

	function testOneSource()
	{
		$router = new \Myiyk\SeoRouter\Router(self::getSource('url', array(
				'Front:Homepage', array(
					'action' => 'show',
					'id' => 123,
				)
			)
		));

		$this->routeIn($router, '/url', 'Front:Homepage', array(
			'action' => 'show',
			'test' => 'testvalue',
			'id' => 123,
		), '/url?test=testvalue');
	}

	function testMultipleSources()
	{
		// first source is empty
		$router = new \Myiyk\SeoRouter\Router(self::getSource('url', NULL));

		// second source have url
		$router->addSource(self::getSource('url', array(
			'Front:Homepage', array(
				'action' => 'show',
				'id' => 123,
			)
		)));

		// third source will never used
		$router->addSource(self::getEmptyNeverCalledSource());

		$this->routeIn($router, '/url', 'Front:Homepage', array(
			'action' => 'show',
			'test' => 'testvalue',
			'id' => 123,
		), '/url?test=testvalue');
	}

	function testGenerateUrlWithBasePath()
	{
		$router = new \Myiyk\SeoRouter\Router(self::getSource('url', array(
			'Front:Homepage', array(
				'action' => 'show',
				'id' => 123,
			)
		)));

		$this->routeIn($router, '/~user/web-root/url', 'Front:Homepage', array(
			'action' => 'show',
			'test' => 'testvalue',
			'id' => 123,
		), '/~user/web-root/url?test=testvalue', '/~user/web-root/index.php');
	}

	function tearDown()
	{
		M::close();
	}
}

(new RouterConstructUrl())->run();
