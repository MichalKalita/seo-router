<?php

/**
 * Test: Myiyk\SeoRouter\Router match url
 */

include __DIR__ . '/../bootstrap.php';
include __DIR__ . '/router.php';

use Mockery as M;

/**
 * No result
 */
$router = new \Myiyk\SeoRouter\Router(new Source());

routeIn($router, '/url', NULL);


/**
 * One source
 */
$router = new \Myiyk\SeoRouter\Router(new Source(
	new \Myiyk\SeoRouter\Action('Presenter:default', array('id' => 123)),
	'url'
));

routeIn($router, '/url', 'Presenter', array(
	'action' => 'default',
	'test' => 'testvalue',
	'id' => 123,
), 'http://example.com/url?test=testvalue');


/**
 * Multiple sources
 */
$router = new \Myiyk\SeoRouter\Router(new Source());
$router->addSource(new Source())->addSource(new Source());

routeIn($router, '/url', NULL);


/**
 * Bad source
 */
$mock = M::mock('Myiyk\SeoRouter\ISource');
$mock->shouldReceive('toAction')->with(M::type('Nette\Http\Url'))->once()->andReturn(array('justArray'));

$router = new \Myiyk\SeoRouter\Router($mock);

\Tester\Assert::exception(function () use ($router) {
	routeIn($router, '/url');
}, '\Myiyk\SeoRouter\BadOutputException');
