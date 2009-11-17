<?php
/**
 * $Id: soap_server.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal
 */
require_once dirname(__FILE__) . '/../../../common/global.inc.php';
require_once dirname(__FILE__) . '/search_source/web_service/content_object_soap_search_server.class.php';

$server = new ContentObjectSoapSearchServer();
$server->run();
?>