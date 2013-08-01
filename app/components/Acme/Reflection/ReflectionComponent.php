<?php
namespace Acme\Reflection;

class ReflectionComponent {
	/**
	 * Obtains all public methods in a class
	 * @param string $service
	 * @return array
	 */
	public function getServiceData($service) {
		$class = new \ReflectionClass($service);
		$reflectionMethods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
		
		$methods = array();
		
		foreach ($reflectionMethods as $m) {
			if (!preg_match('/^__/', $m->getName())) {
				$methods[] = $m->getName();
			}
		}
		
		return array('class' => $service, 'methods' => $methods);
	}
}