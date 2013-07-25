<?php
/**
 * Zenith main configuration file ('development' environment)
 */
$config = array(
		/**
		 * Application dependencies
		 * Additional dependencies for the Bleach\Application class (property => class)
		 */
		'inject' => array('logger' => 'Zenith\Log\DevelopmentLogger',
						  'event'  => 'Zenith\Event\EventManager')
);
