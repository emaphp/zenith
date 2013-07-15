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
 * Application directories
 */
//root directory
define('ROOT_DIR', __DIR__);

//services directory
define('SERVICES_DIR',  ROOT_DIR . '/app/services/');

//configuration directory
define('CONFIG_DIR',    ROOT_DIR . '/app/config/');

//views directory
define('VIEWS_DIR',     ROOT_DIR . '/app/views/');

//cache directory
define('CACHE_DIR',     ROOT_DIR . '/store/cache/');

//twig templates directory
define('TPL_CACHE_DIR', ROOT_DIR . '/store/twig');

//logs directory
define('LOGS_DIR',      ROOT_DIR . '/store/logs/');

/**
 * Validate script configuration
 */
if (!isset($env) || !is_string($env) || empty($env)) {
	throw new \RuntimeException("Environment not found");
}

if (!isset($container) || !is_string($container) || empty($container)) {
	throw new \RuntimeException("Application container not found");
}

/**
 * Initialize Composer autoloader
*/
$loader = require 'vendor/autoload.php';

/**
 * Check main configuration file
*/
//check main configuration file
if (!file_exists('app/config/app.php')) {
	throw new \RuntimeException("No configuration file found.");
}

/**
 * Create application container
 */
$app_container = new $container($env, $loader);
$app_container->configure();

/**
 * Create application instance
 */
$app = Zenith\Application::getInstance();
$app_container->injectAll($app);

//clean global namespace
unset($env);
unset($container);

//run main controller
$controller = new Zenith\MainController();
$controller->service();
?>