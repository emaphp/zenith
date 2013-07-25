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
//working directory
define('ROOT_DIR', getcwd() . DIRECTORY_SEPARATOR);

//application directories
define('APP_DIR', ROOT_DIR . 'app/');
define('SERVICES_DIR', APP_DIR . 'services/');
define('COMPONENTS_DIR', APP_DIR . 'components/');
define('CONFIG_DIR', APP_DIR . 'config/');
define('VIEWS_DIR', APP_DIR . 'views/');

//storage directories
define('STORAGE_DIR', APP_DIR . 'storage/');
define('WSDL_DIR', STORAGE_DIR . 'wsdl/');
define('TWIG_DIR', STORAGE_DIR . 'twig');
define('LOGS_DIR', STORAGE_DIR . 'logs/');

/**
 * Validate script configuration
*/
if (!isset($env) || !is_string($env) || empty($env)) {
	throw new \RuntimeException("Application environment not found");
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
$app_container = new $container();
$app_container['environment'] = $env;
$app_container->configure();

/**
 * Create application instance
*/
$app = Zenith\Application::getInstance();
$app_container->inject($app);

//clean global namespace
unset($env);
unset($container);
unset($app_container);