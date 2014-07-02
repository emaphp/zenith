<?php
include_once 'constants.php';

/**
 * Application environment
 */
$env = 'development';

/**
 * Validate script configuration
*/
if (!isset($env) || !is_string($env) || empty($env)) {
	throw new \RuntimeException("Application environment not found!");
}

/**
 * Initialize Composer autoloader
 */
$loader = require __DIR__ . '/../vendor/autoload.php';

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
$container = new Pimple\Container;
$container['environment'] = $env;
$container['paths'] = require __DIR__ . '/paths.php';
Injector\Injector::inject($app, $container);

//register logger instance
$provider = new Zenith\IoC\Provider\LoggerServiceProvider;
$provider->register($container);

//set application error handler
$errorHandler = new Zenith\Error\ErrorHandler();
Injector\Injector::inject($errorHandler, $container);

//setup error handler methods
$app->setErrorHandler($errorHandler);
set_error_handler([$app->getErrorHandler(), 'error_handler']);
set_exception_handler([$app->getErrorHandler(), 'exception_handler']);
register_shutdown_function([$app->getErrorHandler(), 'shutdown_handler']);

/**
 * Add services/components dir through autoloader
 */
//obtain configuration
$config = $app->load_config('app');

if (is_null($config)) {
	throw new \RuntimeException("No configuration file found!");
}

//setup global namespace
$loader->add('', $app->path('services'));
$loader->add('', $app->path('components'));

//setup custom namespaces
if (array_key_exists('namespaces', $config) && is_array($config['namespaces'])) {
	foreach ($config['namespaces'] as $ns) {
		$loader->add($ns, $app->path('services'));
		$loader->add($ns, $app->path('components'));
	}
}

//clean global namespace
unset($config);
unset($env);
unset($container);
unset($provider);
unset($app);
unset($errorHandler);