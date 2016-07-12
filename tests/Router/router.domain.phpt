<?php

/**
 * Test: Myiyk\SeoRouter\Router working with domains
 */

include __DIR__ . '/../bootstrap.php';
include __DIR__ . '/router.php';

function domainTest($url, $result) {
	// url as a string
	$router = new \Myiyk\SeoRouter\Router(new Source(
		new \Myiyk\SeoRouter\Action('Homepage:default'),
		$url
	));
	routeIn($router, '', 'Homepage', array(
		'action' => 'default',
		'test' => 'testvalue'
	), $result);

	// url as an object
	$router = new \Myiyk\SeoRouter\Router(new Source(
		new \Myiyk\SeoRouter\Action('Homepage:default'),
		new \Nette\Http\Url($url)
	));
	routeIn($router, '', 'Homepage', array(
		'action' => 'default',
		'test' => 'testvalue'
	), $result);
}

/**
 * Domain
 */
domainTest('//domain.tld', 'http://domain.tld/?test=testvalue');
domainTest('http://domain.tld', 'http://domain.tld/?test=testvalue');
domainTest('https://domain.tld', 'http://domain.tld/?test=testvalue');

/**
 * Subdomain
 */
domainTest('//sub2.sub1.domain.tld', 'http://sub2.sub1.domain.tld/?test=testvalue');
domainTest('http://sub2.sub1.domain.tld', 'http://sub2.sub1.domain.tld/?test=testvalue');
domainTest('https://sub2.sub1.domain.tld', 'http://sub2.sub1.domain.tld/?test=testvalue');

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
