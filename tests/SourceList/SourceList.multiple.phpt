<?php

/**
 * Test: Source list
 */

include __DIR__ . '/SourceList.php';


/**
 * Multiple source list in one
 */
$sourceList = new \Myiyk\SeoRouter\SourceList(
	new SourceMock(new \Myiyk\SeoRouter\Action('Ignore:me'), 'no'));
$sourceList->addSource(new SourceMock(new \Myiyk\SeoRouter\Action('Ignore:me'), 'no'));
$sourceList->prependSource(new SourceMock(new \Myiyk\SeoRouter\Action('Ignore:me'), 'no'));


$sourceList2 = new \Myiyk\SeoRouter\SourceList(
	new SourceMock(new \Myiyk\SeoRouter\Action('Ignore:me'), 'no'));
$sourceList2->addSource(new SourceMock(new \Myiyk\SeoRouter\Action('Ignore:me'), 'no'));
$sourceList2->prependSource(new SourceMock(new \Myiyk\SeoRouter\Action('Ignore:me'), 'no'));

$sourceList3 = new \Myiyk\SeoRouter\SourceList(
	new SourceMock(new \Myiyk\SeoRouter\Action('Ignore:me'), 'no'));
$sourceList3->addSource(new SourceMock(new \Myiyk\SeoRouter\Action('Ignore:me'), 'no'));
$sourceList3->prependSource(new SourceMock(new \Myiyk\SeoRouter\Action('Presenter:action'), 'yes'));


$sourceList->addSource($sourceList2);
$sourceList->prependSource($sourceList3);


$action = $sourceList->toAction(new \Nette\Http\Url());
\Tester\Assert::same('Presenter:action', $action->getPresenterAndAction());
\Tester\Assert::same('yes', $sourceList->toUrl(new \Myiyk\SeoRouter\Action('Not:depend')));

/**
 * Infinity inherit
 */
$sourceList = new \Myiyk\SeoRouter\SourceList();
$sourceList2 = new \Myiyk\SeoRouter\SourceList($sourceList);
$sourceList->addSource($sourceList2);

\Tester\Assert::exception(function () use ($sourceList) {
	$sourceList->toAction(new \Nette\Http\Url());
}, 'Myiyk\SeoRouter\Exceptions\InfiniteLoopException');

\Tester\Assert::exception(function () use ($sourceList) {
	$sourceList->toUrl(new \Myiyk\SeoRouter\Action('Not:depend'));
}, 'Myiyk\SeoRouter\Exceptions\InfiniteLoopException');
