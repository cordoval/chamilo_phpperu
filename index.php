<?php
/**
 * $Id: index.php 176 2009-11-12 13:25:10Z vanpouckesven $
 */

$this_section = 'home';

include_once ('common/global.inc.php');

Utilities :: set_application($this_section);

if (Session :: get_user_id())
{
    $usermgr = new UserManager($_SESSION['_uid']);
    $user = $usermgr->get_user();
}
else
{
    $user = null;
}

$hmgr = new HomeManager($user);
$hmgr->render_menu('home');
?>
