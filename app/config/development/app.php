<?php
/**
 * Zenith main configuration file ('development' environment)
 */
return array(
		/**
		 * Application dependencies
		 * Additional dependencies for the Bleach\Application class (property => class)
		 */
		'inject' => array('logger' => 'Zenith\Log\DevelopmentLogger')
);
