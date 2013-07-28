<?php
/**
 * Zenith main configuration file ('production' environment)
 */
return array(
		/**
		 * Application dependencies
		 * Additional dependencies for the Bleach\Application class (property => class)
		 */
		'inject' => array('logger' => 'Zenith\Log\ProductionLogger'),
		/**
		 * Twig configuration
		 * Configuration vars for Twig
		 */
		'twig' => array('cache' => Zenith\Application::getInstance()->path('twig'))
);
