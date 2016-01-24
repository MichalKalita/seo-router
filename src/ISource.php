<?php

namespace Myiyk\SeoRouter;

interface ISource
{
	/**
	 * Translate url to application request
	 * @param string $url
	 * @return \Nette\Application\Request|null
	 */
	public function toAction($url);

	/**
	 * Translate application request to url
	 * @param \Nette\Application\Request $request
	 * @return string|null
	 */
	public function toUrl(\Nette\Application\Request $request);
}
