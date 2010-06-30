<?php
/**
 * @package user.ajax
 */
require_once dirname(__FILE__) . '/../../common/global.inc.php';

if (strpos($_SERVER['REQUEST_URI'], 'leave.php') !== false && strpos($_SERVER['REQUEST_URI'], 'ajax') !== false)
{
    $tracker = Request :: post('tracker');
    $return = Event :: trigger('leave', UserManager :: APPLICATION_NAME, array(VisitTracker :: PROPERTY_ID => $tracker, VisitTracker :: PROPERTY_LOCATION => $_SERVER['REQUEST_URI'], VisitTracker :: PROPERTY_USER_ID => $user->get_id()));
}
?>