Seo-Router
==========
[![Build Status](https://travis-ci.org/Myiyk/seo-router.svg?branch=master)](https://travis-ci.org/Myiyk/seo-router)
[![Latest stable](https://img.shields.io/packagist/v/Myiyk/seo-router.svg)](https://packagist.org/packages/myiyk/seo-router)
[![License](https://img.shields.io/packagist/l/Myiyk/seo-router.svg)](https://github.com/Myiyk/seo-router/blob/master/LICENSE)

Install
-------
Using composer `composer require myiyk/seo-router:@dev`

or

Copy sources somewhere Nette/RobotLoader or Composer will find it

Basic Usage
-----------

Config app/config/config.neon

```yaml
services:
	- App\Model\SeoRouterSource
	routerFactory: App\RouterFactory
	router: @routerFactory::createRouter(@seoRouter.router)

extensions:
	seoRouter: Myiyk\SeoRouter\Extension
```

Router need source with interface Myiyk/SeoRouter/ISource

```php
<?php // file: app/model/SeoRouterSource.php

namespace App\Model;

use Myiyk\SeoRouter\Action;
use Myiyk\SeoRouter\ISource;
use Nette;

class SeoRouterSource extends Nette\Object implements ISource
{

	public function toAction(Nette\Http\Url $url)
	{
		$relativeUrl = $url->getRelativeUrl();

		if ($relativeUrl == '' || $relativeUrl == 'home') {
			$presenter = "Homepage:default";  // example presenter
			$params = array(
				'id' => 123, // other parameters
			);
			return new Action($presenter, $params);
		} else { // or return NULL if result not found
			return NULL;
		}
	}

	public function toUrl(Action $request)
	{
		$presenter = $request->getPresenter();
		$action = $request->getAction();
		// complete documentation of Nette\Application\Request on
		// https://api.nette.org/2.3.8/Nette.Application.Request.html

		if ($presenter == 'Homepage' && $action == 'default') {
			// or $request->getPresenterAndAction() == 'Homepage:default'
			return "home"; // means example.com/home
		} else { // or return NULL if result not found
			return NULL;
		}
	}
}

```

method createRouter in app/router/RouterFactory.php
```php
public function createRouter(SeoRouter\Router $seoRouter)
{
	$router = new RouteList;
	$router[] = $seoRouter;
	$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
	return $router;
}
```