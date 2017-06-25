<?php

namespace Myiyk\SeoRouter\Exceptions;

class InvalidConfigurationException extends \Exception
{
	const NOT_RECOGNIZED = 1;
	const MISSING_SOURCES = 2;
}
