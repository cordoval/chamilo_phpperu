<?php

if(! class_exists('Path')){
	return false;
}

require_once_all(dirname(__FILE__) .'/*.class.php');
require_once_all(dirname(__FILE__) .'/import/*.class.php');
require_once_all(dirname(__FILE__) .'/export/*.class.php');