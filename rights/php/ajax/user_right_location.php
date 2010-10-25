<?php
use common\libraries\Authentication;

use rights\RightsUtilities;
/**
 * $Id: user_right_location.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.ajax
 */
$this_section = 'rights';

require_once dirname(__FILE__) . '/../../common/global.inc.php';

Utilities :: set_application($this_section);

if (! Authentication :: is_valid())
{
    return 0;
}

$user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());
// TODO: User real right_users'n'rights here
if (! $user->is_platform_admin())
{
    echo 0;
}

$rights = $_POST['rights'];
$rights = explode('_', $rights);

$right = $rights['1'];
$right_user = $rights['2'];
$location = $rights['3'];

if (isset($right_user) && isset($right) && isset($location))
{
    $success = RightsUtilities :: invert_user_right_location($right, $right_user, $location);
    if (! $success)
    {
        echo 0;
    }
    else
    {
        echo 1;
    }
}
else
{
    echo 0;
}
?>