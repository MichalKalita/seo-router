<?php

/**
 * Test: Myiyk\SeoRouter\Router basic url constructions
 */

include __DIR__ . '/../bootstrap.php';
include __DIR__ . '/router.php';


/**
 * Not found
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Front:Homepage:show', array('id' => 123)),
	NULL
));

routeIn($router, 'url', 'Front:Homepage', array(
	'action' => 'show',
	'test' => 'testvalue',
	'id' => 123,
));


/**
 * One source
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Front:Homepage:show', array('id' => 123)),
	'url'
));

routeIn($router, '/url', 'Front:Homepage', array(
	'action' => 'show',
	'test' => 'testvalue',
	'id' => 123,
), 'http://example.com/url?test=testvalue');


/**
 * Multiple sources
 */
// first source is empty
$router = new \Myiyk\SeoRouter\Router(new Source(NULL, 'url'));

// second source have url
$router->addSource(new Source(
	new \Myiyk\SeoRouter\Action('Front:Homepage:show', array('id' => 123)),
	'url'
));

// third source will never used
$router->addSource(new Source());

routeIn($router, '/url', 'Front:Homepage', array(
	'action' => 'show',
	'test' => 'testvalue',
	'id' => 123,
), 'http://example.com/url?test=testvalue');


/**
 * Url with base path
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Front:Homepage:show', array('id' => 123)),
	'url'
));

routeIn($router, '/~user/web-root/url', 'Front:Homepage', array(
	'action' => 'show',
	'test' => 'testvalue',
	'id' => 123,
), 'http://example.com/~user/web-root/url?test=testvalue', '/~user/web-root/index.php');
