<?php

namespace Myiyk\SeoRouter;

use Myiyk;
use Nette;

interface ISource
{
	/**
	 * Translate url to application request
	 * @param Nette\Http\Url $url
	 * @return Myiyk\SeoRouter\Action|null
	 */
	public function toAction(Nette\Http\Url $url);

	/**
	 * Translate action to url
	 * @param Myiyk\SeoRouter\Action $action
	 * @return Nette\Http\Url|string|null
	 */
	public function toUrl(Myiyk\SeoRouter\Action $action);
}
