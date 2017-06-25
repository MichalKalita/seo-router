<?php

namespace Myiyk\SeoRouter;

use Myiyk\SeoRouter\Exceptions\InvalidParameterException;
use Nette\Object;

class Action extends Object
{
	/** @var string */
	protected $presenter;
	/** @var string */
	protected $action;
	/** @var array */
	protected $parameters;

	public function __construct($presenterAndAction, $parameters = array())
	{
		$this->setPresenterAndAction($presenterAndAction);
		$this->parameters = $parameters;
	}

	/**
	 * @param mixed $presenterAndAction
	 * @return Action
	 * @throws InvalidParameterException
	 */
	public function setPresenterAndAction($presenterAndAction)
	{
		$presenterAndAction = (string)$presenterAndAction;
		$pos = strrpos($presenterAndAction, ':');
		if ($pos === FALSE) {
			throw new InvalidParameterException("Invalid parameter, '$presenterAndAction' must contains ':'");
		}

		$this->presenter = substr($presenterAndAction, 0, $pos);
		$this->action = substr($presenterAndAction, $pos + 1);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPresenterAndAction()
	{
		return $this->presenter . ':' . $this->action;
	}

	/**
	 * @param array $parameters
	 * @return Action
	 */
	public function setParameters(array $parameters)
	{
		$this->parameters = $parameters;
		return $this;
	}

	/**
	 * @param string $name
	 * @param string|int $value
	 * @return $this
	 */
	public function setParameter($name, $value)
	{
		$this->parameters[$name] = $value;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * Get parameter
	 * @param string|int $name
	 * @return mixed
	 */
	public function getParameter($name)
	{
		return isset($this->parameters[$name]) ? $this->parameters[$name] : NULL;
	}

	/**
	 * @param string $presenter
	 * @return Action
	 */
	public function setPresenter($presenter)
	{
		$this->presenter = (string)$presenter;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPresenter()
	{
		return $this->presenter;
	}

	/**
	 * @param string $action
	 * @return Action
	 */
	public function setAction($action)
	{
		$this->action = (string)$action;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAction()
	{
		return $this->action;
	}
}