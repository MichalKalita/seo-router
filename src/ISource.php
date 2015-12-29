<?php

namespace Myiyk\SeoRouter;

interface ISource
{
	/**
	 * Translate url to application request
	 * @param string $url
	 * @return \Nette\Application\Request|null|false
	 */
	public function toAction($url);

	/**
	 * Translate application request to url
	 * @param \Nette\Application\Request $request
	 * @return url|null|false
	 */
	public function toUrl(\Nette\Application\Request $request);
}
