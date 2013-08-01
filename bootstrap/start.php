<?php
/**
 * Application environment
 */
$env = 'development';

/**
 * Application container
 */
$container = 'Zenith\IoC\ApplicationContainer';

/**
 * Validate script configuration
*/
if (!isset($env) || !is_string($env) || empty($env)) {
	throw new \RuntimeException("Application environment not found!");
}

if (!isset($container) || !is_string($container) || empty($container)) {
	throw new \RuntimeException("Application container not found!");
}

/**
 * Initialize Composer autoloader
 */
$loader = require __DIR__ . '/../vendor/autoload.php';

/**
 * Setup error handler
 */
$errorHandler = new Zenith\Error\ErrorHandler();

//setup error handler methods
set_error_handler(array(&$errorHandler, 'error_handler'));
set_exception_handler(array(&$errorHandler, 'exception_handler'));
register_shutdown_function(array(&$errorHandler, 'shutdown_handler'));

/**
 * Check paths configuration file
 */
//check main configuration file
if (!file_exists(__DIR__ . '/paths.php')) {
	throw new \RuntimeException("Application paths script not found!");
}

/**
 * Create application instance
 */
$app = Zenith\Application::getInstance();

//set application environment and paths
$app->environment = $env;
$app->paths = require __DIR__ . '/paths.php';

/**
 * Add components dir through autoloader
 */
//obtain configuration
$config = $app->load_config('app');

if (is_null($config)) {
	throw new \RuntimeException("No configuration file found!");
}

//setup global namespace
$loader->add('', $app->path('components'));

//setup custom namespaces
if (array_key_exists('namespaces', $config) && is_array($config['namespaces'])) {
	foreach ($config['namespaces'] as $ns) {
		$loader->add($ns, $app->path('components'));
	}
}
/**
 * Create application container
 */
$app_container = new $container();

//setup application logger
$app_container['logger'] = $app_container->share(function ($c) {
	$config = Zenith\Application::getInstance()->load_config('app');
	
	if (!array_key_exists('logger', $config) || !is_string($config['logger']) || empty($config['logger'])) {
		throw new \RuntimeException("No application logger found!");
	}
	
	$logger = new $config['logger'];
	return $logger;
});

//setup application error handler
$app_container['error_handler'] = $app_container->share(function ($c) use ($errorHandler) {
	$errorHandler->logger = $c['logger'];
	return $errorHandler;
});

$app_container->configure();

/**
 * Inject additional dependencies
 */
$app_container->inject($app);

//clean global namespace
unset($config);
unset($env);
unset($container);
unset($app_container);