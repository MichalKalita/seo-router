<?php

/**
 * Test: Myiyk\SeoRouter\Router with OneWay
 */

include __DIR__ . '/../bootstrap.php';
include __DIR__ . '/router.php';


/**
 * OneWay as a flag
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Front:Homepage:show'),
	'url'
), array(), \Myiyk\SeoRouter\Router::ONE_WAY);

routeIn($router, '/url', 'Front:Homepage', array(
	'action' => 'show',
	'test' => 'testvalue'
), NULL);


/**
 * OneWay as an option
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Front:Homepage:show'),
	'url'
), array('oneWay' => true));

routeIn($router, '/url', 'Front:Homepage', array(
	'action' => 'show',
	'test' => 'testvalue'
), NULL);
