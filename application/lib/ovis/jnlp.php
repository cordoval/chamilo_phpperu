<?php
/*
 * creates a java webstart for the uploader application
 */
include_once ('../../../common/global.inc.php');
require_once Path :: get_application_path().'lib/ovis/ovis_utilities.class.php';

 OvisUtilities :: print_jnlp(
	$_GET['username'], $_GET['password'],
	Path :: get('WEB_APP_PATH') . 'lib/ovis/webservices/webservice.class.php?wdsl',
	Path :: get('WEB_APP_PATH') . 'lib/ovis/',
        'jnlp.php?' . $_SERVER['QUERY_STRING'],
	Path :: get('WEB_APP_PATH').'lib/ovis/jar');
?>