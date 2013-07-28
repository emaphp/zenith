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
 * Create application container
 */
$app_container = new $container();
$app_container->configure();

/**
 * Inject additional dependencies
 */
$app_container->inject($app);

//clean global namespace
unset($env);
unset($container);
unset($app_container);