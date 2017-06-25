<?php

namespace Myiyk\SeoRouter;

use Myiyk;
use Myiyk\SeoRouter\Exceptions\BadOutputException;
use Myiyk\SeoRouter\Exceptions\InfiniteLoopException;
use Nette;
use Nette\Http\Url;
use Nette\Object;

/**
 * Class SourceList
 * @package Myiyk\SeoRouter
 */
class SourceList extends Object implements ISource
{

	/** @var ISource[] */
	private $sources = array();

	/** @var bool */
	private $working = false;

	/**
	 * SourceList constructor.
	 * @params ISource
	 */
	public function __construct(/* sources */)
	{
		foreach (func_get_args() as $source) {
			$this->addSource($source);
		}
	}

	/**
	 * Add source
	 * @param ISource $source
	 * @return $this
	 */
	public function addSource(ISource $source)
	{
		$this->sources[] = $source;
		return $this;
	}

	/**
	 * Translate url to application request
	 * @param Nette\Http\Url $url
	 * @return Action|null
	 * @throws InfiniteLoopException
	 * @throws \Exception
	 */
	public function toAction(Nette\Http\Url $url)
	{
		if ($this->working) {
			throw new InfiniteLoopException("This SourceList is child of child");
		}
		$this->working = true;

		$result = NULL;

		try {
			foreach ($this->sources as $source) {
				if ($result = $source->toAction($url)) {
					if (!$result instanceof Action) {
						throw new BadOutputException(
							get_class($source) . '::toAction() must return Myiyk\SeoRouter\Action, not '
							. (is_object($result) ? get_class($result) : gettype($result))
						);
					}
					break;
				}
			}
		} catch (\Exception $e) {
			$this->working = false;
			throw $e;
		}

		$this->working = false;
		return $result;
	}

	/**
	 * Translate action to url
	 * @param Myiyk\SeoRouter\Action $action
	 * @return Url|null|string
	 * @throws InfiniteLoopException
	 * @throws \Exception
	 */
	public function toUrl(Myiyk\SeoRouter\Action $action)
	{
		if ($this->working) {
			throw new InfiniteLoopException("This SourceList is child of child");
		}
		$this->working = true;

		try {
			foreach ($this->sources as $source) {
				if ($result = $source->toUrl($action)) {
					if (!$result instanceof Url && !is_string($result)) {
						throw new BadOutputException(
							get_class($source) . '::toUrl() must return Nette\Http\Url or string, not '
							. (is_object($result) ? get_class($result) : gettype($result))
						);
					}
					return $result;
				}
			}
		} catch (\Exception $e) {
			$this->working = false;
			throw $e;
		}

		$this->working = false;
		return NULL;
	}

	/**
	 * Prepend source
	 * @param ISource $source
	 * @return $this
	 */
	public function prependSource(ISource $source)
	{
		$this->sources = array($source) + $this->sources;
		return $this;
	}
}
