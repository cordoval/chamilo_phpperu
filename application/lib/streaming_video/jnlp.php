<?php
/*
 * creates a java webstart for the uploader application
 */
include_once ('../../../common/global.inc.php');
require_once Path :: get_application_path().'lib/streaming_video/streaming_video_utilities.class.php';

 StreamingVideoUtilities :: print_jnlp(
	$_GET['username'], $_GET['password'],
	Path :: get('WEB_APP_PATH') . 'lib/streaming_video/webservices/webservice.class.php?wdsl',
	Path :: get('WEB_APP_PATH') . 'lib/streaming_video/',                                            
        'jnlp.php?' . $_SERVER['QUERY_STRING'],
	Path :: get('WEB_APP_PATH').'lib/streaming_video/signed_jar');                                   
?>