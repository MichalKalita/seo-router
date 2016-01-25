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
	const ACTION_IN_PRESENTER = 'actionInPresenter',
		IGNORE_IN_QUERY = 'ignoreInQuery',
		IGNORE_URL = 'ignoreUrl',
		PRESENTER = 'presenter';

	/** @var ISource[] */
	protected $sources = array();

	protected $options = array(
		self::ACTION_IN_PRESENTER => FALSE, // presenter name contains action
		self::IGNORE_IN_QUERY => array('presenter', 'action', 'id'), // parameters ignored from query
		self::IGNORE_URL => array(), // array of ignored url
		self::PRESENTER => NULL, // default presenter
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
	 * @param string $url
	 * @return Request|null
	 * @throws BadOutputException
	 */
	protected function toAction($url)
	{
		$result = NULL;

		foreach ($this->sources as $source) {
			if ($result = $source->toAction($url)) {
				if (!$result instanceof Request) {
					throw new BadOutputException(
						get_class($source) . '::toAction() must return Nette\Application\Request, not '
						. (is_object($result) ? get_class($result) : gettype($result))
					);
				}
				break;
			}
		}

		return $result;
	}

	/**
	 * @param Request $appRequest
	 * @return null|string
	 */
	protected function toUrl(Request $appRequest)
	{
		foreach ($this->sources as $source) {
			if ($result = $source->toUrl($appRequest))
				return $result;
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

		if ($request = $this->toAction((string)$path)) {
			$params = array_merge($httpRequest->getQuery(), $request->getParameters());
			$presenter = $request->getPresenterName();

			// presenter not set from ISource, load from parameters or default presenter
			if (!mb_strlen($presenter)) {
				if (isset($params[self::PRESENTER]) && $params[self::PRESENTER]) {
					$presenter = $params[self::PRESENTER];
				} elseif ($this->options[self::PRESENTER]) {
					$presenter = $this->options[self::PRESENTER];
				} else {
					return NULL;
				}
			}
			unset($params[self::PRESENTER]);

			// find action name in presenter, if it in enable in options
			if (!isset($params['action']) && $this->options['actionInPresenter']) {
				$splitter = strrpos($presenter, ':');
				if ($splitter !== FALSE) {
					$params['action'] = substr($presenter, $splitter + 1);
					$presenter = substr($presenter, 0, $splitter);
				}
			}

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
		if ($this->options['oneWay']) {
			return NULL;
		}

		if ($slug = $this->toUrl($appRequest)) {
			$params = $this->clearParameters($appRequest->getParameters());

			$url = (($this->options['secured']) ? 'https' : 'http') . '://' .
				$refUrl->getAuthority() . $refUrl->getBasePath() . $slug;

			$sep = ini_get('arg_separator.input');
			$query = http_build_query($params, '', $sep ? $sep[0] : '&');
			if ($query != '') { // intentionally ==
				$url .= '?' . $query;
			}
			return $url;
		}
		return NULL;
	}

	public function loadOptions(array $new)
	{
		$result = $this->options;

		if (array_key_exists(self::ACTION_IN_PRESENTER, $new)) {
			$result[self::ACTION_IN_PRESENTER] = $new[self::ACTION_IN_PRESENTER];
			unset($new[self::ACTION_IN_PRESENTER]);
		}

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

		if (array_key_exists(self::PRESENTER, $new)) {
			$result[self::PRESENTER] = $new[self::PRESENTER];
			unset($new[self::PRESENTER]);
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
