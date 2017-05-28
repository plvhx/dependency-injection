<?php

@trigger_error("This autoloader just for dummy testing. Just run 'composer dump-autoload --optimize' and you're set.", E_USER_DEPRECATED);

\spl_autoload_register(function($className) {
	$rootDir = __DIR__ . DIRECTORY_SEPARATOR . 'src';

	$namespace = 'Experiments\\DependencyInjection';

	$className = str_replace(
		'\\',
		DIRECTORY_SEPARATOR,
		str_replace($namespace, $rootDir, $className)
	);

	$className .= '.php';

	if (file_exists($className) && is_file($className)) {
		clearstatcache(true);

		require_once $className;
	}
});