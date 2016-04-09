<?php

/**
 * Test: Myiyk\SeoRouter\Router with Secure
 */

include __DIR__ . '/../bootstrap.php';
include __DIR__ . '/router.php';


/**
 * Secured as a flag
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Front:Homepage:show'),
	'url'
), array(), \Myiyk\SeoRouter\Router::SECURED);

routeIn($router, '/url', 'Front:Homepage', array(
	'action' => 'show', 
	'test' => 'testvalue'
), 'https://example.com/url?test=testvalue');


/**
 * Secured as an option
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Front:Homepage:show'),
	'url'
), array('secured' => true));

routeIn($router, '/url', 'Front:Homepage', array(
	'action' => 'show',
	'test' => 'testvalue'
), 'https://example.com/url?test=testvalue');
