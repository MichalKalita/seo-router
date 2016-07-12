<?php

/**
 * Common code for Route test cases.
 */

use Tester\Assert;

function routeIn(Nette\Application\IRouter $route, $url,
                 $expectedPresenter = NULL, $expectedParams = NULL, $expectedUrl = NULL,
                 $scriptPath = NULL)
{
	$url = new Nette\Http\UrlScript("http://example.com$url");
	if ($scriptPath) {
		$url->setScriptPath($scriptPath);
	}
	if ($url->getQueryParameter('presenter') === NULL) {
		$url->setQueryParameter('presenter', 'querypresenter');
	}
	$url->appendQuery([
		'test' => 'testvalue',
	]);

	$httpRequest = new Nette\Http\Request($url);

	$request = $route->match($httpRequest);

	if ($request) { // matched
		$params = $request->getParameters();
		asort($params);
		asort($expectedParams);
		Assert::same($expectedPresenter, $request->getPresenterName());
		Assert::same($expectedParams, $params);

		$result = $route->constructUrl($request, $url);
		Assert::same($expectedUrl, $result);

	} else { // not matched
		Assert::null($expectedPresenter);
	}
}

function routeOut(Nette\Application\IRouter $route, $presenter, $params = array())
{
	$url = new Nette\Http\Url('http://example.com');
	$request = new Nette\Application\Request($presenter, 'GET', $params);
	return $route->constructUrl($request, $url);
}

class Source implements \Myiyk\SeoRouter\ISource
{
	/** @var array|\Myiyk\SeoRouter\Action|null  */
	private $action;
	/** @var \Nette\Http\Url|null|string  */
	private $url;

	/**
	 * Source constructor.
	 * @param \Myiyk\SeoRouter\Action|array|NULL $action
	 * @param \Nette\Http\Url|string|NULL $url
	 */
	function __construct($action = NULL, $url = NULL)
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