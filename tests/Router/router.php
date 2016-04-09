<?php

/**
 * Common code for Route test cases.
 */

use Mockery as M;
use Myiyk\SeoRouter\Action;
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

		unset($params['extra']);
		$request->setParameters($params);
		$result = $route->constructUrl($request, $url);
		$result = strncmp($result, 'http://example.com', 18) ? $result : substr($result, 18);
		Assert::same($expectedUrl, $result);

	} else { // not matched
		Assert::null($expectedPresenter);
	}
}

class RouterBaseTest extends \Tester\TestCase
{
	/**
	 * @param string $url
	 * @param null|array $request
	 * @param null|int $toUrlCount
	 * @param null|int $toActionCount
	 * @return \Myiyk\SeoRouter\ISource
	 */
	static function getSource($url, $request = NULL, $toUrlCount = NULL, $toActionCount = NULL)
	{
		if ($request !== NULL && isset($request[1]) && isset($request[1]['action'])) {
			$r = new Action(
				$request[0] . ':' . $request[1]['action'], // presenter name
				isset($request[1]) ? $request[1] : array() // parameters
			);
		} else {
			$r = NULL;
		}

		$mock = M::mock('Myiyk\SeoRouter\ISource');
		$mock->shouldReceive('toUrl')
			// ->with($r) // cannot be used, because bug in mockery https://github.com/padraic/mockery/pull/527
			->with(M::type('Myiyk\SeoRouter\Action'))->times($toUrlCount)->andReturn($url);
		$mock->shouldReceive('toAction')
			->with(M::type('Nette\Http\Url'))->times($toActionCount)->andReturn($r);
		return $mock;
	}

	static function getEmptyNeverCalledSource()
	{
		return self::getSource(NULL, NULL, 0, 0);
	}

	static function routeIn(Nette\Application\IRouter $route, $url,
	                        $expectedPresenter = NULL, $expectedParams = NULL, $expectedUrl = NULL,
	                        $scriptPath = NULL)
	{
		routeIn($route, $url, $expectedPresenter, $expectedParams, $expectedUrl, $scriptPath);
	}


	static function routeOut(Nette\Application\IRouter $route, $presenter, $params = [])
	{
		$url = new Nette\Http\Url('http://example.com');
		$request = new Nette\Application\Request($presenter, 'GET', $params);
		return $route->constructUrl($request, $url);
	}

	function tearDown()
	{
		M::close();
	}
}