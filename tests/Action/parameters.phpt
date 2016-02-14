<?php

use Tester\Assert;

include __DIR__ . '/../bootstrap.php';

$action = new \Myiyk\SeoRouter\Action('Homepage:default', array('id' => 123));

Assert::same(array('id' => 123), $action->getParameters());
Assert::same(123, $action->getParameter('id'));
Assert::null($action->getParameter('missing'));

Assert::same($action, $action->setParameter('missing', 'value'));
Assert::same(array('id' => 123, 'missing' => 'value'), $action->getParameters());
Assert::same('value', $action->getParameter('missing'));

