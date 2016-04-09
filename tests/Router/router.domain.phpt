<?php

/**
 * Test: Myiyk\SeoRouter\Router working with domains
 */

include __DIR__ . '/../bootstrap.php';
include __DIR__ . '/router.php';

/**
 * Domain
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Homepage:default'),
	new \Nette\Http\Url('http://example.com')
));
routeIn($router, '', 'Homepage', array(
	'action' => 'default', 
	'test' => 'testvalue'
), 'http://example.com/?test=testvalue');

/**
 * Subdomain
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Homepage:default'),
	new \Nette\Http\Url('http://subdomain.example.com')
));
routeIn($router, '', 'Homepage', array(
	'action' => 'default', 
	'test' => 'testvalue'
), 'http://subdomain.example.com/?test=testvalue');

/**
 * Subdomain
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Homepage:default'),
	(new \Nette\Http\Url())->setPath('url')->setQueryParameter('extra', 'extravalue')
));
routeIn($router, '', 'Homepage', array(
	'action' => 'default', 
	'test' => 'testvalue'
), 'http://example.com/url?extra=extravalue&test=testvalue');
