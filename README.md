zenith
======

The PHP-SOAP Framework
<br/>

**Author**: Emmanuel Antico<br/>
**Last Modification**: 2014/10/15<br/>
**Version**: 2.0

Introduction
------------
<br/>
**What's Zenith?**

Zenith is a small framework aimed to provide a fast way to develop and deploy SOAP applications in PHP. 

**How does it work?**

Zenith already comes with a WSDL file which defines a default operation named *execute*. This operation defines a request composed by three main sections:

- **Service**: It describes which service is called, that is, the class and the method to invoke.
- **Configuration**: Provides a way to change a service behaviour by defining an arbitrary number of execution options.
- **Parameter**: This section is used to define which parameters are sent to the service. Any valid XML can be used as a parameter.

<br/>
Calling the *execute* operation will generate a generic response composed in another three sections:

- **Service**: The class and method invoked.
- **Status**: Describes the execution status. A status code different from 0 means that the execution was not successful. An additional message can be provided to describe the status.
- **Result**: Contains the data returned from the service. A service must return a valid XML string.

<br/>
A request to a Zenith application is first handled by a dispatcher class and then by a service. Services work as the controller layer of an application. Once a service is executed, the generated response is sent back.

Defining a default operation allow us to provide a generic way to call to any service stored within our application. This flexibility does come with a price: In order to support any type of parameter (and return any type of response) the service must parse the incoming XML (and generate a XML response). Zenith provides some tools to obtain a parameter as a *DOMDocument*/*SimpleXMLElement* object and rendering XML through PHP and Twig.


<br/>
Installation
------------
<br/>

Zenith requires PHP 5.4 + the php-soap extension. Installation is done by following these simple steps:
- Download this project as a zip. Extract the file to a folder of your like.
- Go to your application folder and open a terminal.
- Get Composer
```
$ curl -sS https://getcomposer.org/installer | php
```
- Dowload required packages
```
$ php composer.phar install
```

<br/>
Dependencies
------------
<br/>
Zenith will install the following packages as dependencies:

 * [Symfony's Console Component](https://github.com/symfony/Console "")
 * [Monolog](https://github.com/Seldaek/monolog "")
 * [Twig](https://github.com/fabpot/Twig "")
 * [Injector](https://github.com/emaphp/Injector "")

<br/>
Setup
-----

<br/>
In order to store logs and templates the webserver must have write access to some folders.

```bash
$ sudo chown :www-data app/store/logs app/store/twig
```

If you're working with nginx and that doesn't work try setting the user.

```bash
$ sudo chown www-data:www-data app/store/logs app/store/twig
```

<br/>
Application folders
-------------------
<br/>
All Zenith applications contain the same folders, which are located inside *app/*.

- **config**: This folder contains all configuration scripts needed by the application. It also contains the environment folders.
- **storage**: It contains the application logs (*storage/logs/*), the generated WSDL files (*storage/wsdl/*) and the cache files used by Twig (*storage/twig/*).
- **views**: This folder contains all views files used to generate a response. It also contains the view used to generate the application WSDL.
- **services**: Services classes needed by your application.
- **components**: Any additional classes that provides some functionality but aren't services.

<br/>
The Bleach CLI
--------------
<br/>
Zenith also comes with a small command line interface called **bleach**. **bleach** provides 2 utilities:

- It can generate a WSDL file right away by obtaining your current app configuration.
- It can also generate a service just by providing its class name. Generated classes are stored in *app/services*.

<br/>
The first thing we need to do right after the installation is to create the proper WSDL for our application. We do this by running the following command:

```bash
$ php bleach wsdl-create
```

This command will generate a WSDL file called *application.wsdl*, which you'll find in *app/storage/wsdl*. This file includes the *execute* operation definition and can be configured through the *app/config/wsdl.php* script. To change the service endpoint you must edit the value defined by the key **uri**, which you'll find within the template arguments array (see the description for the wdsl.php configuration file). In order to generate a new WSDL file and overwrite the old one, we must run the same command with the *--force* option, like this:

```bash
$ php bleach wsdl-create --force
```
To test if the WSDL creation was successful try to open the URL pointing to your application folder and adding *'service.php?wsdl'* at the end. If the creation was successful then the server should return the WSDL file contents.

<br/>
Services
--------

<br/>
We create a new service by calling the service-create command through **bleach**.
```bash
$ php bleach service-create MyService
```
This command will generate a new file called *MyService.php* in the services folder. If the class already exists then you can always use the --force option to overwrite it.
```bash
$ php bleach service-create MyService --force
```
The file will look like the following:

```php
<?php
use Zenith\SOAPService;

/**
 * Dependencies (add more providers to inject object properties):
 *
 * @Provider Zenith\IoC\Provider\ViewServiceProvider
 * @Provider Zenith\IoC\Provider\LoggerServiceProvider
 */
class MyService extends SOAPService {
}
?>
```
Zenith uses [Injector](https://github.com/emaphp/Injector "") to inject dependencies into services classes. All services contain a *Monolog/Logger* instance and a *Zenith\View\View* component ready to use.

<br/>
**Adding methods**

<br/>
We can also tell which methods must be included by putting their names right after the class name. The following command will generate the same service with 2 additional methods: *makeCoffee* and *doLaundry*.
```bash
$ php bleach service-create MyService makeCoffee doLaundry
``` 

The resulting code will look like this:

```php
<?php
use Zenith\SOAPService;
use Zenith\SOAP\Request;
use Zenith\SOAP\Response;

/**
 * Dependencies (add more providers to inject object properties):
 *
 * @Provider Zenith\IoC\Provider\ViewServiceProvider
 * @Provider Zenith\IoC\Provider\LoggerServiceProvider
 */
class MyService extends SOAPService {
	public function makeCoffee(Request $request, Response $response) {
	}

	public function doLaundry(Request $request, Response $response) {
	}
}
?>
```
<br/>
**Namespaces**

<br/>
When a namespace is specified, the generated script will be stored within a new folder. This command will generate a folder called *MyCompany/* inside the services directory with the script containing the generated class.

```bash
$ php bleach service-create MyCompany/MyService makeCoffee doLaundry
``` 
The resulting code will look like this:
```php
<?php
namespace MyCompany;

use Zenith\SOAPService;
use Zenith\SOAP\Request;
use Zenith\SOAP\Response;

/**
 * Dependencies (add more providers to inject object properties):
 *
 * @Provider Zenith\IoC\Provider\ViewServiceProvider
 * @Provider Zenith\IoC\Provider\LoggerServiceProvider
 */
class MyService extends SOAPService {
	public function makeCoffee(Request $request, Response $response) {
	}

	public function doLaundry(Request $request, Response $response) {
	}
}
?>
```
Note: Whenever you declare a new namespace make sure to include it in the ***namespaces*** configuration array, located in the *app/config/app.php* script.

<br/>
Environments
------------
<br/>
Application environments are easy to setup. In order to create a new environment add a new folder inside *app/config/* and name it after the new environment. Applications already come with 2 default environments: *development* and *production*. To change your application environment open the *boostrap/start.php* script and then change the value stored in the **$env** variable with the desired environment. Values that are contained in an environment configuration script are merged against the ones in the main folder.

<br/>
Configuration
-------------
<br/>
Configuration files are scripts stored in *app/config/* that contain values that change the application behaviour. These values are defined as simple hash tables. The framework makes use of 4 main scripts, which are:

- **app.php**: For common configuration.
- **wsdl.php**: These values define how the application WSDL is generated.
- **server.php**: Values to configure the *SoapServer* instance.
- **logger.php**: Configures the logger instance generated within a service.

<br/>
**The app.php configuration file**

- **dispatcher**: This key defines the dispatcher class. The dispatcher class is the one that implements the *execute* operation (or whatever operations are defined in the application WSDL file). Its default value is *Zenith\Dispatcher\Dispatcher*, which is the default dispatcher class.
- **namespaces**: This key defines an array containing all namespaces that are associated with the *services/* and *components/* folder. Classes that are declared within those namespaces will be automatically included using the Composer autoloader, as long they are stored in the *app/services*  and *app/components* folder.
- **twig**: These are the configuration values for Twig. Check Twig documentation for a more detailed list of values (http://twig.sensiolabs.org/documentation).

<br/>
**The wsdl.php configuration file**

- **template**: The view file to use for rendering the application WSDL.
- **args**: The arguments passed to the WSDL view.

<br/>
**The server.php configuration file**

- **wsdl**: Which WSDL file to use. WSDL files are stored in *app/storage/wsdl*. The *wsdl-create* command obtains the WSDL filename from this value.
- **options**: Additional options passed to the *SoapServer* constructor.

<br/>
**The logger.php configuration file**

Logger options are heavily associated to the current execution environment, that's why you won't find it in the *config/* folder but in an environment folder. The logger.php file supports the following options:

 - **path**: A custom file path were log messages are stored. By default, this path is automatically generated.
 - **threshold**: What level of messages are going to be stored.

<br/>
Loading a configuration file
----------------------------
<br/>
Loading values from a configuration script is done by calling the *load_config* method in the *Zenith\Application* class, specifying the script name as a parameter. This example code shows how to load the main configuration script.

```php
<?php
$config = \Zenith\Application::getInstance()->load_config('app');
$dispatcher = $config['dispatcher'];
```

<br/>
Custom configuration
-------------
<br/>
You can add your own configuration scripts, as long as they respect the same syntax. The following script returns a set of options required by a weather service which must return the current temperature measured in differents scales.
```php
<?php
// File: temperature.php
// Options used by the weather service

return [
    'scale' => 'F' // return values in Farenheit
];
```
Getting those values requires obtaining the current application instance.

```php
<?php
use Zenith\Application;

$config = Application::getInstance()->load_config('temperature');
$scale = $config['scale'];
```

<br/>
Views
-----
<br/>
Views are files used to render a response. Zenith supports 2 types of views:

- Generic views: These views are regular PHP scripts that receive their parameters from within a service. They must be named with the *.php* extension.
- Twig views: Twig is a popular template engine for PHP. All Twig views must have a *.twig* extension. Check out the Twig documentation site to know how to design your own templates.

<br/>
Calling a view is pretty straightforward, just call the *render* method in the *view* component. Arguments need to be passed as an array.

```php
return $this->view->render('product', ['id' => 3526, 'name' => 'Red dress', 'size' => 'S']);
```

<br/>
Logs
-----
<br/>
All services are initialized with a *Monolog\Logger* instance. Log messages are stored according with the message level of the current environment.

```php
// add notice
$this->logger->addNotice("Something happened...");
```

<br/>
The HelloWorld service
----------------------

<br/>
To illustrate a service implementation, Zenith already comes with an example service called *HelloWorld*, which you can find in *app/services/Acme*. This class shows features like:

- Returning a simple string response.
- Generating a view through PHP and Twig.
- Modifying a response status on the fly.
- Loading a configuration value.
- Creating an object stored in the components folder.
- Obtaining a parameter as a *DOMDocument* object.
- Obtaining a parameter as a *SimpleXMLElement* object.
- Throwing a *SoapFault*.
- Throwing an exception with a predefined status code and message.

<br/>
The request for each method can be found above its declaration. You can play around and test this class to get a better understanding of the framework. Remember to delete this class once your application goes into production environment.

<br/>
License
-------
<br/>
This code is licensed under the BSD 2-Clause license.