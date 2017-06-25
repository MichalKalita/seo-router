<?php

use Tester\Assert;

include __DIR__ . '/../bootstrap.php';

$action = new \Myiyk\SeoRouter\Action('Homepage:default');

Assert::same('Homepage:default', $action->getPresenterAndAction());
Assert::same('Homepage', $action->getPresenter());
Assert::same('default', $action->getAction());

Assert::same($action, $action->setPresenter('Articles'));
Assert::same('Articles:default', $action->getPresenterAndAction());
Assert::same('Articles', $action->getPresenter());
Assert::same('default', $action->getAction());

Assert::same($action, $action->setAction('view'));
Assert::same('Articles:view', $action->getPresenterAndAction());
Assert::same('Articles', $action->getPresenter());
Assert::same('view', $action->getAction());

Assert::same($action, $action->setPresenter('Admin:Dashboard'));
Assert::same('Admin:Dashboard:view', $action->getPresenterAndAction());
Assert::same('Admin:Dashboard', $action->getPresenter());
Assert::same('view', $action->getAction());

Assert::same($action, $action->setPresenterAndAction('Admin:Product:edit'));
Assert::same('Admin:Product:edit', $action->getPresenterAndAction());
Assert::same('Admin:Product', $action->getPresenter());
Assert::same('edit', $action->getAction());

Assert::exception(function () use ($action) {
	$action->setPresenterAndAction('PresenterWithoutAction');
}, 'Myiyk\SeoRouter\Exceptions\InvalidParameterException');
