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

use Nette;

class SeoRouterSource extends Nette\Object implements \Myiyk\SeoRouter\ISource
{

	// for address example.com/home slug will be "home"
	public function toAction($slug)
	{
		if ($slug == '' || $slug == 'home') {
			$presenter = "Homepage";  // example presenter
			$params = array(
				'action' => 'default', // action
				'id' => 123,           // other parameters
			);
			return new \Nette\Application\Request($presenter, NULL, $params);
		} else { // or return NULL if result not found
			return NULL;
		}
	}

	public function toUrl(\Nette\Application\Request $request)
	{
		$presenter = $request->getPresenterName();
		$params = $request->getParameters();
		// complete documentation of Nette\Application\Request on
		// https://api.nette.org/2.3.8/Nette.Application.Request.html

		if ($presenter == 'Homepage' && $params['action'] == 'default') {
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