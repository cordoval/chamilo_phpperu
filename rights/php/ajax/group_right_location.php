<?php
namespace rights;

use user\UserDataManager;

use common\libraries\Session;
use common\libraries\Utilities;
use common\libraries\Authentication;

use rights\RightsUtilities;
/**
 * $Id: group_right_location.php 214 2009-11-13 13:57:37Z vanpouckesven $
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
// TODO: User real right_groups'n'rights here
if (! $user->is_platform_admin())
{
    echo 0;
}

$rights = $_POST['rights'];
$rights = explode('_', $rights);

$right = $rights['1'];
$right_group = $rights['2'];
$location = $rights['3'];

if (isset($right_group) && isset($right) && isset($location))
{
    $success = RightsUtilities :: invert_group_right_location($right, $right_group, $location);
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