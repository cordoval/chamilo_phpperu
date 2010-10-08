<?php
/**
 * $Id: index.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.authentication.shibboleth
 */
require_once dirname(__FILE__) . '/shibboleth_authentication.class.php';

$shibAuth = new ShibbolethAuthentication();
$shibAuth->check_login();

?>