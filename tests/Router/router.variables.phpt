<?php

include __DIR__ . '/../bootstrap.php';
include __DIR__ . '/router.php';

/**
 * Variable %domain%
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Presenter:action', array('id' => 123)),
	'//subdomain.%domain%'
));
routeIn($router, '/url', 'Presenter', array(
	'action' => 'action',
	'id' => 123,
	'test' => 'testvalue',
), 'http://subdomain.example.com/?test=testvalue');


/**
 * Variable %tld%
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Presenter:action', array('id' => 123)),
	'//domain.%tld%'
));
routeIn($router, '/url', 'Presenter', array(
	'action' => 'action',
	'id' => 123,
	'test' => 'testvalue',
), 'http://domain.com/?test=testvalue');


/**
 * Variable %sld%
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Presenter:action', array('id' => 123)),
	'//%sld%.cz'
));
routeIn($router, '/url', 'Presenter', array(
	'action' => 'action',
	'id' => 123,
	'test' => 'testvalue',
), 'http://example.cz/?test=testvalue');


/**
 * Variables %sld% and %tld%
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Presenter:action', array('id' => 123)),
	'//%sld%.%tld%'
));
routeIn($router, '/url', 'Presenter', array(
	'action' => 'action',
	'id' => 123,
	'test' => 'testvalue',
), 'http://example.com/?test=testvalue');


/**
 * Variable %host%
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Presenter:action', array('id' => 123)),
	'//%host%'
));
routeIn($router, '/url', 'Presenter', array(
	'action' => 'action',
	'id' => 123,
	'test' => 'testvalue',
), 'http://example.com/?test=testvalue');
