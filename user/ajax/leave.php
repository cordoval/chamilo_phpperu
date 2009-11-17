<?php
/**
 * $Id: leave.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.ajax
 */
require_once dirname(__FILE__) . '/../../common/global.inc.php';
$tracker = $_POST['tracker'];
$return = Events :: trigger_event('leave', 'user', array('tracker' => $tracker, 'location' => $_SERVER['REQUEST_URI'], 'user' => $user, 'event' => 'leave'));
//echo $tracker;
?>
