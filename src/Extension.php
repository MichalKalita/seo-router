<?php

namespace Myiyk\SeoRouter;

use Nette;

/**
 * Class Extension
 * @package Myiyk\SeoRouter
 */
class Extension extends Nette\DI\CompilerExtension
{

	/** @var bool */
	private $specificSourceList = FALSE;

	public function loadConfiguration()
	{
		$config = $this->getConfig();

		// configuration of sources exist
		if (array_key_exists('sources', $config)) {
			$this->specificSourceList = TRUE;
			$sources = $config['sources'];
			unset($config['sources']);
		} else {
			// default source
			$sources = array('@Myiyk\SeoRouter\ISource');
		}

		$builder = $this->getContainerBuilder();
		$router = $builder->addDefinition($this->prefix('router'))
			->setClass('Myiyk\SeoRouter\Router', array(array_shift($sources), $config))
			->setAutowired(FALSE);

		// add other sources
		foreach ($sources as $source) {
			$router->addSetup('addSource', array($source));
		}
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		// if configuration of sources missing, check if exist exactly one service Myiyk\SeoRouter\ISource
		if (!$this->specificSourceList) {
			try {
				$source = $builder->getByType('Myiyk\SeoRouter\ISource');
				if ($source === NULL) {
					throw new Nette\DI\MissingServiceException(
						'Service type Myiyk\SeoRouter\ISource not found, did you register it?'
					);
				}
			} catch (Nette\DI\ServiceCreationException $e) {
				// example message: Multiple services of type Myiyk\SeoRouter\ISource found: 15_EmptySource, 17
				if (strpos($e->getMessage(), 'Multiple services') !== FALSE) {
					throw new InvalidConfigurationException(
						"Add configuration of sources ( {$this->name}: sources: )",
						InvalidConfigurationException::MISSING_SOURCES, // error code
						$e // previous exception
					);
				} else {
					throw $e;
				}
			}
		}
	}
}