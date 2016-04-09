<?php

require __DIR__ . '/../bootstrap.php';

function getTempDir()
{
	return __DIR__ . '/../temp/' . Nette\Utils\Random::generate();
}

function createFile($extension, $content)
{
	$filename = getTempDir() . $extension;
	file_put_contents($filename, $content);
	return $filename;
}

class EmptySource implements \Myiyk\SeoRouter\ISource
{
	public function toAction(\Nette\Http\Url $url)
	{
		return NULL;
	}

	public function toUrl(\Myiyk\SeoRouter\Action $action)
	{
		return NULL;
	}
}