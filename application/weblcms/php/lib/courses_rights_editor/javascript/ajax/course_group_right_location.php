<?php
namespace application\weblcms;

use rights\RightsDataManager;
use user\UserDataManager;

use common\libraries\Authentication;
use common\libraries\Session;
use common\libraries\Utilities;
use common\libraries\Request;

/**
 * $Id: group_right_location.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.ajax
 */
$this_section = 'rights';

require_once dirname(__FILE__) . '/../../../../../../common/global.inc.php';
require_once dirname(__FILE__) . '/../../../weblcms_rights.class.php';

Utilities :: set_application($this_section);

if (! Authentication :: is_valid())
{
    return 0;
}

$user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());
// TODO: User real right_course_groups'n'rights here
if (! $user->is_platform_admin())
{
    echo 0;
}

$locations = Request :: post('locations');
//small fix for tags
$locations = str_replace('\\"', '"', $locations);
$locations = json_decode($locations);

$rights = $_POST['rights'];
$rights = explode('_', $rights);

$right = $rights['1'];
$right_course_group = $rights['2'];

if (isset($right_course_group) && isset($right) && isset($locations) && count($locations) > 0)
{
    $success = true;

    $rdm = RightsDataManager :: get_instance();

    foreach ($locations as $location_id)
    {
        $success &= WeblcmsRights :: invert_course_group_right_location($right, $right_course_group, $location_id);
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