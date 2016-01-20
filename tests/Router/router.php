<?php

/**
 * Common code for Route test cases.
 */

use Mockery as M;
use Nette\Application\Request;
use Tester\Assert;

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
		if ($request === NULL) {
			$r = NULL;
		} else {
			$r = new Request(
				$request[0], // presenter name
				NULL,
				isset($request[1]) ? $request[1] : array() // parameters
			);
		}

		$mock = M::mock('Myiyk\SeoRouter\ISource');
		$mock->shouldReceive('toUrl')
			// ->with($r) // cannot be used, because bug in mockery https://github.com/padraic/mockery/pull/527
			->with(M::type('Nette\Application\Request'))->times($toUrlCount)->andReturn($url);
		$mock->shouldReceive('toAction')
			->with($url)->times($toActionCount)->andReturn($r);
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