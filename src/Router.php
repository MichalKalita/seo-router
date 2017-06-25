<?php

namespace Myiyk\SeoRouter;

use Myiyk\SeoRouter\Exceptions\InvalidConfigurationException;
use Nette;
use Nette\Application\Request;
use Nette\Http\Url;
use Nette\Object;

/**
 * Class Router
 * @package Myiyk\SeoRouter
 */
class Router extends Object implements Nette\Application\IRouter
{
	/** options */
	const IGNORE_IN_QUERY = 'ignoreInQuery',
		IGNORE_URL = 'ignoreUrl';

	/** @var ISource */
	protected $source;

	protected $options = array(
		self::IGNORE_IN_QUERY => array('presenter', 'action', 'id'), // parameters ignored from query
		self::IGNORE_URL => array(), // array of ignored url
		'secured' => FALSE,
		'oneWay' => FALSE,
	);

	public function __construct(ISource $source, array $options = array(), $flags = 0)
	{
		if ($source instanceof SourceList) {
			$this->source = $source;
		} else {
			$this->source = new SourceList($source);
		}

		if ($flags & self::SECURED) {
			$options['secured'] = TRUE;
		}
		if ($flags & self::ONE_WAY) {
			$options['oneWay'] = TRUE;
		}

		$this->loadOptions($options);
	}

	/**
	 * Add source
	 * @param ISource $source
	 * @return $this
	 */
	public function addSource(ISource $source)
	{
		$this->source->addSource($source);

		return $this;
	}

	/**
	 * Prepend source
	 * @param ISource $source
	 * @return $this
	 */
	public function prependSource(ISource $source)
	{
		$this->source->prependSource($source);

		return $this;
	}

	/**
	 * @param array $params
	 * @return array
	 */
	protected function clearParameters(array $params)
	{
		foreach ($params as $p => $_value) {
			if (in_array($p, $this->options[self::IGNORE_IN_QUERY])) {
				unset($params[$p]);
			}
		}
		return $params;
	}

	/**
	 * @param Nette\Http\IRequest $httpRequest
	 * @return Request|null
	 */
	public function match(Nette\Http\IRequest $httpRequest)
	{
		$url = $httpRequest->getUrl();
		$path = substr($url->path, strlen($url->basePath));

		if (in_array($path, $this->options[self::IGNORE_URL])) {
			return NULL;
		}

		if ($action = $this->source->toAction($url)) {
			$params = array_merge($httpRequest->getQuery(), $action->getParameters());
			$presenter = $action->getPresenter();
			$params['action'] = $action->getAction();

			// presenter not set from ISource, load from parameters or default presenter
			if (!mb_strlen($presenter)) {
				if (isset($params['presenter']) && $params['presenter']) {
					$presenter = $params['presenter'];
				} else {
					return NULL;
				}
			}
			unset($params['presenter']);

			return new Request($presenter,
				$httpRequest->getMethod(), $params,
				$httpRequest->getPost(), $httpRequest->getFiles()
			);
		}
		return NULL;
	}

	/**
	 * @param Request $appRequest
	 * @param Url $refUrl
	 * @return null|string
	 */
	public function constructUrl(Request $appRequest, Url $refUrl)
	{
		// one way can't generate link
		if ($this->options['oneWay']) {
			return NULL;
		}

		$params = $this->clearParameters($appRequest->getParameters());

		$action = new Action($appRequest->getPresenterName() . ':' . $appRequest->getParameter('action'), $params);

		// ISource return NULL, not found url to generate
		if (($seoUrl = $this->source->toUrl($action)) === NULL) {
			return NULL;
		}

		if (!$seoUrl instanceof Url) {
			$seoUrl = new Url($seoUrl);
		}

		// host
		if ($seoUrl->getHost()) {
			$host = $refUrl->getHost();
			$parts = ip2long($host) ? [$host] : array_reverse(explode('.', $host));
			$host = strtr($seoUrl->getHost(), [
				// '/%basePath%/' => $refUrl->getBasePath(), // TODO: add support
				'%tld%' => $parts[0],
				'%domain%' => isset($parts[1]) ? "$parts[1].$parts[0]" : $parts[0],
				'%sld%' => isset($parts[1]) ? $parts[1] : '',
				'%host%' => $refUrl->getHost(),
			]);
		} else {
			$host = $refUrl->getHost();
		}

		// path
		$path = $seoUrl->getPath();

		// query
		$query = $seoUrl->getQueryParameters() + $params;
		ksort($query);
		$seoUrl->setQuery($query);
		$query = $seoUrl->getQuery();

		// fragment
		$fragment = $seoUrl->getFragment();

		return ($this->options['secured'] ? 'https' : 'http') . '://' . // protocol
		$host .
		$refUrl->getBasePath() .
		($path === '/' ? '' : $path) .
		($query ? '?' . $query : '') .
		($fragment ? '#' . $fragment : '');
	}

	/**
	 * @param array $new
	 * @throws InvalidConfigurationException
	 */
	public function loadOptions(array $new)
	{
		$result = $this->options;

		if (array_key_exists(self::IGNORE_IN_QUERY, $new)) {
			if (is_array($new[self::IGNORE_IN_QUERY])) {
				$result[self::IGNORE_IN_QUERY] = $new[self::IGNORE_IN_QUERY];
			} else {
				$result[self::IGNORE_IN_QUERY] = array();
			}
			unset($new[self::IGNORE_IN_QUERY]);
		}

		if (array_key_exists(self::IGNORE_URL, $new)) {
			if (is_array($new[self::IGNORE_URL])) {
				$result[self::IGNORE_URL] = $new[self::IGNORE_URL];
			} else {
				$result[self::IGNORE_URL] = array();
			}
			unset($new[self::IGNORE_URL]);
		}

		if (array_key_exists('secured', $new)) {
			$result['secured'] = (bool)$new['secured'];
			unset($new['secured']);
		}

		if (array_key_exists('oneWay', $new)) {
			$result['oneWay'] = (bool)$new['oneWay'];
			unset($new['oneWay']);
		}

		if (count($new)) {
			throw new InvalidConfigurationException(
				'Options are not recognized. ' . print_r($new, true),
				InvalidConfigurationException::NOT_RECOGNIZED
			);
		}

		$this->options = $result;
	}

}
