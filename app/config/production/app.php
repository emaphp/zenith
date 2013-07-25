<?php
/**
 * Zenith main configuration file ('production' environment)
 */
$config = array(
		/**
		 * Application dependencies
		 * Additional dependencies for the Bleach\Application class (property => class)
		 */
		'inject' => array('logger' => 'Zenith\Log\ProductionLogger',
						  'event'  => 'Zenith\Event\EventManager'),
		/**
		 * Twig configuration
		 * Configuration vars for Twig
		 */
		'twig' => array('cache' => TWIG_DIR)
);
