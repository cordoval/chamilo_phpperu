<?php
/**
 * $Id: chat_manager.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.ajax
 */
require_once dirname(__FILE__) . '/../../common/global.inc.php';

Translation :: set_application('user');

$from_user_id = Request :: post('from_user_id');
$to_user_id = Request :: post('to_user_id');
$action = Request :: post('action');
$message = Request :: post('message');
$last_message_date = Request :: post('last_message_date');

$udm = UserDataManager :: get_instance();
$from_user = $udm->retrieve_user($from_user_id);
$to_user = $udm->retrieve_user($to_user_id);

$cm = new ChatManager($from_user, $to_user, null);

switch($action)
{
	case 'send_message':
		$cm->send_message($message);
		break;
	case 'retrieve_messages':
		echo $cm->to_xml($last_message_date);
		break;
}

?>