<?php

$gitDir = 'd:/school/KPPRO/Futsal';
$container = require $gitDir . '/app/bootstrap.php';

$container->getByType(Nette\Application\Application::class)
	->run();
