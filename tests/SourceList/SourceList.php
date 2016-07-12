<?php

include __DIR__ . '/../bootstrap.php';

/**
 * Mock to interface ISource
 */
class SourceMock implements \Myiyk\SeoRouter\ISource
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