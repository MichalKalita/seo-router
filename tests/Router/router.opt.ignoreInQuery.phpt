<?php

/**
 * Test: Myiyk\SeoRouter\Router option 'ignoreInQuery'
 */

include __DIR__ . '/../bootstrap.php';
include __DIR__ . '/router.php';


/**
 * Option ignoreInQuery have default value
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Front:Homepage:default', array('id' => 123)),
	'url'
));

routeIn($router, '/url?action=queryaction&id=queryid', 'Front:Homepage', array(
	'action' => 'default',
	'id' => 123,
	'test' => 'testvalue',
), 'http://example.com/url?test=testvalue');


/**
 * Option ignoreInQuery is empty array
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Front:Homepage:default', array('id' => 123)),
	'url'
), array('ignoreInQuery' => array()));

routeIn($router, '/url?action=queryaction&id=queryid', 'Front:Homepage', array(
	'action' => 'default',
	'id' => 123,
	'test' => 'testvalue',
), 'http://example.com/url?action=default&id=123&test=testvalue');


/**
 * Option ignoreInQuery is NULL
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Front:Homepage:default', array('id' => 123)),
	'url'
), array('ignoreInQuery' => NULL));

routeIn($router, '/url?action=queryaction&id=queryid', 'Front:Homepage', array(
	'action' => 'default',
	'id' => 123,
	'test' => 'testvalue',
), 'http://example.com/url?action=default&id=123&test=testvalue');


/**
 * Option ignoreInQuery ignore all parameters
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Front:Homepage:default', array('id' => 123)),
	'url'
), array('ignoreInQuery' => array('action', 'presenter', 'test', 'id')));

routeIn($router, '/url?action=queryaction&id=queryid', 'Front:Homepage', array(
	'action' => 'default',
	'id' => 123,
	'test' => 'testvalue',
), 'http://example.com/url');
