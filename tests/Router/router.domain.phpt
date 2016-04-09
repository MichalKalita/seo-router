<?php

/**
 * Test: Myiyk\SeoRouter\Router working with domains
 */

include __DIR__ . '/../bootstrap.php';
include __DIR__ . '/router.php';

use Mockery as M;

class Source implements \Myiyk\SeoRouter\ISource
{

	/** @var \Myiyk\SeoRouter\Action */
	private $action;
	/** @var \Nette\Http\Url|string|null */
	private $url;

	function __construct(\Myiyk\SeoRouter\Action $action, $url)
	{
		$this->action = $action;
		$this->url = $url;
	}

	public function toAction(\Nette\Http\Url $url)
	{
		return $this->action;
	}

	public function toUrl(\Myiyk\SeoRouter\Action $request)
	{
		return $this->url;
	}
}

// domain
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Homepage:default'),
	new \Nette\Http\Url("http://example.com")
));
routeIn($router, '', 'Homepage', array('action' => 'default', 'test' => 'testvalue'),
	'/?test=testvalue');

// subdomain
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Homepage:default'),
	new \Nette\Http\Url("http://subdomain.example.com")
));
routeIn($router, '', 'Homepage', array('action' => 'default', 'test' => 'testvalue'),
	'http://subdomain.example.com/?test=testvalue');

// subdomain
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Homepage:default'),
	(new \Nette\Http\Url())->setPath('url')->setQueryParameter('extra', 'extravalue')
));
routeIn($router, '', 'Homepage', array('action' => 'default', 'test' => 'testvalue'),
	'/url?extra=extravalue&test=testvalue');
