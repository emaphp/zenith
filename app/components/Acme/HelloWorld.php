<?php
namespace Acme;

use Zenith\Service;
use Zenith\SOAP\Request;
use Zenith\SOAP\Response;
use Zenith\Exception\ServiceException;

class HelloWorld extends Service {
	/**
	 * Generic response
	 * @param Request $request
	 * @param Response $response
	 * @return string
	 */
	public function hello(Request $request, Response $response) {
		return 'Hello World :)';
	}
	
	/**
	 * Renders a template through the 'view' property
	 * @param Request $request
	 * @param Response $response
	 */
	public function sayHi(Request $request, Response $response) {
		return $this->view->render('Acme/hello_world', array('message' => 'Hello World!!!', 'destination' => 'Earth'));
	}

	/**
	 * Obtains a configuration value from Request
	 * @param Request $request
	 * @param Response $response
	 */
	public function sayGoodbye(Request $request, Response $response) {
		//obtain option 'lang'
		$lang = $request->option('lang');
		$args = array('message' => 'Goodbye World!!!', 'destination' => 'Earth');
		
		if ($lang == 'sp') {
			$args = array('message' => 'Adios Mundo!!!', 'destination' => 'Tierra');
		}
		elseif (!empty($lang) && $lang != 'en') {
			//log notice
			Zenith\Application::getInstance()->logger->addNotice("Unrecognized language '$lang'");
		}
		
		return $this->view->render('Acme/hello_world', $args);
	}
	
	/**
	 * Obtains class public methods through the Acme/ReflectionComponent class
	 */
	public function expose(Request $request, Response $response) {
		$component = new Reflection\ReflectionComponent();
		$data = $component->getServiceData(get_class($this));
		return $this->view->render('Acme/expose', $data);
	}
	
	/**
	 * Parses request to a simplexml and DOMDocument objects
	 * @param Request $request
	 * @param Response $response
	 */
	public function parseRequest(Request $request, Response $response) {
		$xml = $request->getParameter(Request::AS_SIMPLEXML);
		$user_id = (int) $xml->id;
		
		$dom = $request->getParameter(Request::AS_DOM);
		$name = $dom->getElementsByTagName('name')->item(0);
		
		//set response status and result
		$response->setStatus(0, 'XML parsed correctly');
		$response->setResult($this->view->render('Acme/user', array('user_id' => $user_id, 'user_name' => $name->nodeValue)));
	}
	
	public function throw_fault(Request $request, Response $response) {
		throw new \SoapFault("Server", "Unexpected error");
	}
	
	public function throw_exception(Request $request, Response $response) {
		throw new \Exception("Something bad happened...");
	}
	
	public function throw_service_exception(Request $request, Response $response) {
		throw new ServiceException(5, "A customized error response");
	}
}
?>