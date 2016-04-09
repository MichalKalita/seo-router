<?php

namespace Myiyk\SeoRouter;

use Nette;
use Nette\Application\Request;
use Nette\Http\Url;
use Nette\Object;

// TODO: promenne v URL, napr. 'domain', aby slo adresovat subdomeny
class Router extends Object implements Nette\Application\IRouter
{
	/** options */
	const IGNORE_IN_QUERY = 'ignoreInQuery',
		IGNORE_URL = 'ignoreUrl';

	/** @internal url type */
	const HOST = 1,
		PATH = 2,
		RELATIVE = 3;

	/** @var ISource[] */
	protected $sources = array();

	protected $options = array(
		self::IGNORE_IN_QUERY => array('presenter', 'action', 'id'), // parameters ignored from query
		self::IGNORE_URL => array(), // array of ignored url
		'secured' => FALSE,
		'oneWay' => FALSE,
	);

	function __construct(ISource $source, array $options = array(), $flags = 0)
	{
		$this->addSource($source);

		if ($flags & self::SECURED) {
			$options['secured'] = TRUE;
		}
		if ($flags & self::ONE_WAY) {
			$options['oneWay'] = TRUE;
		}

		$this->loadOptions($options);
	}

	public function addSource(ISource $source)
	{
		$this->sources[] = $source;
		return $this;
	}

	/**
	 * @param Url $url
	 * @return Action|null
	 * @throws BadOutputException
	 */
	protected function toAction($url)
	{
		$result = NULL;

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

		return $result;
	}

	/**
	 * @param Action $action
	 * @return null|string|Url
	 * @throws BadOutputException
	 */
	protected function toUrl(Action $action)
	{
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
		return NULL;
	}

	protected function clearParameters($params)
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

		if ($action = $this->toAction($url)) {
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
		if (($seoUrl = $this->toUrl($action)) === NULL) {
			return NULL;
		}

		if ($seoUrl instanceof Url) {
			// host
			$host = $seoUrl->getHost() ? $seoUrl->getHost() : $refUrl->getHost();
			// path
			$path = $seoUrl->getPath();
			// query
			foreach ($params as $key => $value) {
				$seoUrl->setQueryParameter($key, $value);
			}

			$query = $seoUrl->getQuery();
			// fragment
			$fragment = $seoUrl->getFragment();
		} else {
			// host
			$host = $refUrl->getHost();
			$queryPosition = strpos($seoUrl, '?');
			$fragmentPosition = strrpos($seoUrl, '#');
			// path
			$path = $queryPosition ? substr($seoUrl, 0, $queryPosition) : $seoUrl;
			// query
			if ($queryPosition) {
				$query = $fragmentPosition ?
					substr($seoUrl, $queryPosition, $fragmentPosition - $queryPosition) :
					substr($seoUrl, $queryPosition);
				parse_str($query, $query);
			} else {
				$query = array(); // address does not have query
			}

			$query = $query + $params;
			ksort($query);
			$query = http_build_query($query);

			// fragment
			$fragment = $fragmentPosition ? substr($seoUrl, $fragmentPosition) : NULL;
		}

		return ($this->options['secured'] ? 'https' : 'http') . '://' . // protocol
		$host .
		$refUrl->getBasePath() .
		($path === '/' ? '' : $path) .
		($query ? '?' . $query : '') .
		($fragment ? '#' . $fragment : '');
	}

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
				'Options not recognized. ' . print_r($new, true),
				InvalidConfigurationException::NOT_RECOGNIZED
			);
		}

		$this->options = $result;
	}

}
