<?php

namespace Myiyk\SeoRouter;

class InvalidConfigurationException extends \Exception
{
	const NOT_RECOGNIZED = 1;
	const MISSING_SOURCES = 2;
}

class BadOutputException extends \Exception
{
}
