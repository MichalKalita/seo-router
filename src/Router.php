<?php

namespace Myiyk\SeoRouter;

use Nette;
use Nette\Object;

// TODO: nastaveni, ktere parametry nedavat do path
// TODO: promenne v URL, napr. 'domain', aby slo adresovat subdomeny
class Router extends Object implements Nette\Application\IRouter
{
	/** @var ISource[] */
	protected $sources = array();
	/** @var string */
	protected $defaultPresenter = 'Homepage';

	function __construct(ISource $source)
	{
		$this->addSource($source);
	}

	public function addSource(ISource $source)
	{
		$this->sources[] = $source;
		return $this;
	}

	/**
	 * @param string $url
	 * @return false|null|Nette\Application\Request
	 */
	protected function toAction($url)
	{
		foreach ($this->sources as $source) {
			if ($result = $source->toAction($url))
				return $result;
		}
		return false;
	}

	/**
	 * @param Nette\Application\Request $appRequest
	 * @return false|null|Nette\Application\Request
	 */
	protected function toUrl(Nette\Application\Request $appRequest)
	{
		foreach ($this->sources as $source) {
			if ($result = $source->toUrl($appRequest))
				return $result;
		}
		return false;
	}

	/**
	 * @param Nette\Http\IRequest $httpRequest
	 * @return Nette\Application\Request|null
	 */
	public function match(Nette\Http\IRequest $httpRequest)
	{
		$url = $httpRequest->getUrl();
		$path = substr($url->path, strlen($url->scriptPath));

//		if (!$path) { // TODO: tohle musi delat samotny zdroj
//			return NULL;
//		}

		// TODO: podpora jazyku pres nastaveni
		if (preg_match("~^(?'lang'\\w{2})/(?'path'.*)$~U", $path, $matches)) {
			$lang = $matches['lang'];
			$path = $matches['path'];
		} else {
			$lang = 'en';
		}

		if ($request = $this->toAction((string)$path)) {
			$query = $httpRequest->getQuery();

			$params = $request->getParameters();
//			$splitter = strrpos($request->getPresenterName(), ':');
//			if ($splitter !== FALSE) {
//				$params['action'] = substr($request->getPresenterName(), $splitter + 1);
//				$presenter = substr($request->getPresenterName(), 0, $splitter);
//			} else {
//				$params['action'] = $request->getPresenterName();
//				$presenter = $this->defaultPresenter;
//			}
			if (!isset($params['locale']) || !$params['locale']) {
				$params['locale'] = $lang;
			}

			return new \Nette\Application\Request($request->getPresenterName(),
				$httpRequest->getMethod(), array_merge($params, $query),
				$httpRequest->getPost(), $httpRequest->getFiles()
			);
		}
		return NULL;
	}

	/**
	 * @param Nette\Application\Request $appRequest
	 * @param Nette\Http\Url $refUrl
	 * @return null|string
	 */
	public function constructUrl(Nette\Application\Request $appRequest, Nette\Http\Url $refUrl)
	{
		$params = $appRequest->getParameters();

		// TODO: pridat nastaveni jazyku
		// $lang = (isset($params['locale']) && $params['locale'] != 'en') ? ($params['locale'] . '/') : NULL;

		if ($slug = $this->toUrl($appRequest)) {
			// TODO: pridat nastaveni pro ignorovane parametry
			unset($params['id'], $params['action'], $params['locale']);

			// TODO: support https
			$url = 'http://' . $refUrl->getAuthority() . $refUrl->getPath() /* . $lang */ . $slug;

			$sep = ini_get('arg_separator.input');
			$query = http_build_query($params, '', $sep ? $sep[0] : '&');
			if ($query != '') { // intentionally ==
				$url .= '?' . $query;
			}
			return $url;
		}
		return null;
	}

}
