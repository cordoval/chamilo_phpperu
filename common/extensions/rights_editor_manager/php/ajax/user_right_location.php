<?php
namespace common\extensions\rights_editor_manager;

use common\libraries\Utilities;
use common\libraries\Authentication;
use common\libraries\Request;
use common\libraries\Session;

use user\UserDataManager;

use rights\RightsDataManager;
use rights\RightsUtilities;

/**
 * $Id: user_right_location.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.ajax
 */
$this_section = 'rights';

require_once dirname(__FILE__) . '/../../../../global.inc.php';

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

$locations = Request :: post('locations');
//small fix for tags
$locations = str_replace('\\"', '"', $locations);
$locations = json_decode($locations);

$rights = Request :: post('rights');
$rights = explode('_', $rights);

$right = $rights['1'];
$right_user = $rights['2'];

if (isset($right_user) && isset($right) && isset($locations) && count($locations) > 0)
{
    $success = true;

    $rdm = RightsDataManager :: get_instance();

    foreach ($locations as $location_id)
    {
        $success &= RightsUtilities :: invert_user_right_location($right, $right_user, $location_id);
    }

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