<?php

/**
 * Test: Myiyk\SeoRouter\Router option 'ignoreUrl'
 */

include __DIR__ . '/../bootstrap.php';
include __DIR__ . '/router.php';


/**
 * Option ignoreUrl is empty array
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Front:Homepage:default', array('id' => 123)),
	'url'
), array('ignoreUrl' => array()));

routeIn($router, '/url', 'Front:Homepage', array(
	'action' => 'default',
	'id' => 123,
	'test' => 'testvalue',
), 'http://example.com/url?test=testvalue');


/**
 * Option ignoreUrl is NULL
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Front:Homepage:default', array('id' => 123)),
	'url'
), array('ignoreUrl' => NULL));

routeIn($router, '/url', 'Front:Homepage', array(
	'action' => 'default',
	'id' => 123,
	'test' => 'testvalue',
), 'http://example.com/url?test=testvalue');


/**
 * Option ignoreUrl in work
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Front:Homepage:default', array('id' => 123)),
	'url'
), array('ignoreUrl' => array('url')));

routeIn($router, '/url', NULL);
