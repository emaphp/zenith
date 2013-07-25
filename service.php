<?php
//include bootstrap file
require 'bootstrap/start.php';

//run main controller
$controller = new Zenith\MainController();
$controller->service();
?>