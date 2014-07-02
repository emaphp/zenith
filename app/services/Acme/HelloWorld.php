<?php
namespace Acme;

use Zenith\SOAPService;
use Zenith\SOAP\Request;
use Zenith\SOAP\Response;
use Zenith\Exception\SOAPServiceException;

class HelloWorld extends SOAPService {
	/**
	 * Generic response
	 * @param Request $request
	 * @param Response $response
	 * @return string
	 * 
	 * <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:ZenithService">
	 *     <soapenv:Header/>
	 *     <soapenv:Body>
	 *         <urn:execute soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
	 *             <service xsi:type="urn:Service">
	 *                 <class xsi:type="xsd:string">Acme/HelloWorld</class>
	 *                 <method xsi:type="xsd:string">hello</method>
	 *             </service>
	 *             <configuration xsi:type="urn:Configuration"/>
	 *			   <parameter xsi:type="urn:Parameter"/>
	 *		   </urn:execute>
	 *    </soapenv:Body>
	 * </soapenv:Envelope>
	 */
	public function hello(Request $request, Response $response) {
		return 'Hello World :)';
	}
	
	/**
	 * Renders a template through the 'view' property
	 * @param Request $request
	 * @param Response $response
	 * 
	 * <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:ZenithService">
	 *     <soapenv:Header/>
	 *     <soapenv:Body>
	 *         <urn:execute soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
	 *             <service xsi:type="urn:Service">
	 *                 <class xsi:type="xsd:string">Acme/HelloWorld</class>
	 *                 <method xsi:type="xsd:string">sayHi</method>
	 *             </service>
	 *             <configuration xsi:type="urn:Configuration"/>
	 *             <parameter xsi:type="urn:Parameter">World</parameter>
	 *         </urn:execute>
	 *     </soapenv:Body>
	 * </soapenv:Envelope>
	 */
	public function sayHi(Request $request, Response $response) {
		//get parameter as a simple string
		$to = $request->getParameter();
		return "Hello $to!!!";
	}

	/**
	 * Obtains a configuration value from Request
	 * @param Request $request
	 * @param Response $response
	 * 
	 * <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:ZenithService">
	 *     <soapenv:Header/>
	 *     <soapenv:Body>
	 *         <urn:execute soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
	 *             <service xsi:type="urn:Service">
	 *                 <class xsi:type="xsd:string">Acme/HelloWorld</class>
	 *                 <method xsi:type="xsd:string">sayGoodbye</method>
	 *             </service>
	 *             <configuration xsi:type="urn:Configuration">
	 *                 <option xsi:type="urn:Option">
	 *                     <name xsi:type="xsd:string">lang</name>
	 *                     <value xsi:type="xsd:string">sp</value>
	 *                 </option>
	 *             </configuration>
	 *             <parameter xsi:type="urn:Parameter"/>
	 *         </urn:execute>
	 *     </soapenv:Body>
	 * </soapenv:Envelope>
	 */
	public function sayGoodbye(Request $request, Response $response) {
		//obtain option 'lang'
		$lang = $request->getOption('lang');
		$args = ['message' => 'Goodbye World!!!', 'destination' => 'Earth'];
		
		if ($lang == 'sp') {
			$args = ['message' => 'Adios Mundo!!!', 'destination' => 'Tierra'];
		}
		elseif (!empty($lang) && $lang != 'en') {
			//log notice
			$this->logger->addNotice("Unrecognized language '$lang'");
		}
		
		return $this->view->render('Acme/goodbye', $args);
	}
	
	/**
	 * Obtains class public methods through the Acme/ReflectionComponent class
	 * @param Request $request
	 * @param Response $response
	 * 
	 * <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:ZenithService">
	 *     <soapenv:Header/>
	 *     <soapenv:Body>
	 *         <urn:execute soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
	 *             <service xsi:type="urn:Service">
	 *                 <class xsi:type="xsd:string">Acme/HelloWorld</class>
	 *                 <method xsi:type="xsd:string">expose</method>
	 *             </service>
	 *             <configuration xsi:type="urn:Configuration"/>
	 *             <parameter xsi:type="urn:Parameter"/>
	 *         </urn:execute>
	 *     </soapenv:Body>
	 * </soapenv:Envelope>
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
	 * 
	 * <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:ZenithService">
	 *     <soapenv:Header/>
	 *     <soapenv:Body>
	 *         <urn:execute soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
	 *             <service xsi:type="urn:Service">
	 *                 <class xsi:type="xsd:string">Acme/HelloWorld</class>
	 *                 <method xsi:type="xsd:string">parseRequest</method>
	 *             </service>
	 *             <configuration xsi:type="urn:Configuration"/>
	 *             <parameter xsi:type="urn:Parameter">
	 *                 <user>
	 *                     <id>1234</id>
	 *                     <name>jdoe</name>
	 *                 </user>
	 *             </parameter>
	 *         </urn:execute>
	 *     </soapenv:Body>
	 * </soapenv:Envelope>
	 */
	public function parseRequest(Request $request, Response $response) {
		$xml = $request->getParameter(Request::AS_SIMPLEXML);
		$user_id = (int) $xml->id;
		
		$dom = $request->getParameter(Request::AS_DOM);
		$name = $dom->getElementsByTagName('name')->item(0);
		
		//set response status and result
		$response->setStatus(0, 'XML parsed correctly');
		$response->setResult($this->view->render('Acme/user', ['user_id' => $user_id, 'user_name' => $name->nodeValue]));
	}
	
	/**
	 * Throws a SOAP Fault
	 * @param Request $request
	 * @param Response $response
	 * @throws \SoapFault
	 * 
	 * <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:ZenithService">
	 *     <soapenv:Header/>
	 *     <soapenv:Body>
	 *         <urn:execute soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
	 *             <service xsi:type="urn:Service">
	 *                 <class xsi:type="xsd:string">Acme/HelloWorld</class>
	 *                 <method xsi:type="xsd:string">throw_fault</method>
	 *             </service>
	 *             <configuration xsi:type="urn:Configuration"/>
	 *             <parameter xsi:type="urn:Parameter"/>
	 *         </urn:execute>
	 *     </soapenv:Body>
	 * </soapenv:Envelope>
	 */
	public function throw_fault(Request $request, Response $response) {
		throw new \SoapFault("Server", "Unexpected error");
	}
	
	/**
	 * Throws an exception
	 * @param Request $request
	 * @param Response $response
	 * @throws \Exception
	 * 
	 * <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:ZenithService">
	 *     <soapenv:Header/>
	 *     <soapenv:Body>
	 *         <urn:execute soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
	 *             <service xsi:type="urn:Service">
	 *                 <class xsi:type="xsd:string">Acme/HelloWorld</class>
	 *                 <method xsi:type="xsd:string">throw_exception</method>
	 *             </service>
	 *             <configuration xsi:type="urn:Configuration"/>
	 *             <parameter xsi:type="urn:Parameter"/>
	 *         </urn:execute>
	 *     </soapenv:Body>
	 * </soapenv:Envelope>
	 */
	public function throw_exception(Request $request, Response $response) {
		throw new \Exception("Something bad happened...");
	}
	
	/**
	 * Throws a custom service exception
	 * @param Request $request
	 * @param Response $response
	 * @throws ServiceException
	 * 
	 * <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:ZenithService">
	 *     <soapenv:Header/>
	 *     <soapenv:Body>
	 *         <urn:execute soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
	 *             <service xsi:type="urn:Service">
	 *                 <class xsi:type="xsd:string">Acme/HelloWorld</class>
	 *                 <method xsi:type="xsd:string">throw_service_exception</method>
	 *             </service>
	 *             <configuration xsi:type="urn:Configuration"/>
	 *             <parameter xsi:type="urn:Parameter"/>
	 *         </urn:execute>
	 *     </soapenv:Body>
	 * </soapenv:Envelope>
	 */
	public function throw_service_exception(Request $request, Response $response) {
		throw new SOAPServiceException(5, "A customized error response");
	}
}
?>