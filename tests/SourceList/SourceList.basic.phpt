<?php

/**
 * Test: Source list
 */

include __DIR__ . '/SourceList.php';

/**
 * Empty source list
 */
$sourceList = new \Myiyk\SeoRouter\SourceList();
\Tester\Assert::null($sourceList->toAction(new \Nette\Http\Url()));
\Tester\Assert::null($sourceList->toUrl(new \Myiyk\SeoRouter\Action('P:a')));

/**
 * Standard initialize way
 */
$sourceList = new \Myiyk\SeoRouter\SourceList(
	new SourceMock(new \Myiyk\SeoRouter\Action('Presenter:action'), 'a'),
	new SourceMock(new \Myiyk\SeoRouter\Action('Ignore:me'), 'b'),
	new SourceMock(new \Myiyk\SeoRouter\Action('Ignore:me'), 'c')
);

$action = $sourceList->toAction(new \Nette\Http\Url());
\Tester\Assert::same('Presenter:action', $action->getPresenterAndAction());
\Tester\Assert::same('a', $sourceList->toUrl(new \Myiyk\SeoRouter\Action('Not:depend')));


/**
 * Add sources after initialize
 */
$sourceList = new \Myiyk\SeoRouter\SourceList(
	new SourceMock(new \Myiyk\SeoRouter\Action('Presenter:action'), 'a')
);
$sourceList->addSource(new SourceMock(new \Myiyk\SeoRouter\Action('Ignore:me'), 'b'));
$sourceList->addSource(new SourceMock(new \Myiyk\SeoRouter\Action('Ignore:me'), 'c'));

$action = $sourceList->toAction(new \Nette\Http\Url());
\Tester\Assert::same('Presenter:action', $action->getPresenterAndAction());
\Tester\Assert::same('a', $sourceList->toUrl(new \Myiyk\SeoRouter\Action('Not:depend')));


/**
 * Using prepend to initialize
 */
$sourceList = new \Myiyk\SeoRouter\SourceList(
	new SourceMock(new \Myiyk\SeoRouter\Action('Ignore:me'), 'a')
);
$sourceList->prependSource(new SourceMock(new \Myiyk\SeoRouter\Action('Ignore:me'), 'b'));
$sourceList->prependSource(new SourceMock(new \Myiyk\SeoRouter\Action('Presenter:action'), 'c'));

$action = $sourceList->toAction(new \Nette\Http\Url());
\Tester\Assert::same('Presenter:action', $action->getPresenterAndAction());
\Tester\Assert::same('c', $sourceList->toUrl(new \Myiyk\SeoRouter\Action('Not:depend')));
