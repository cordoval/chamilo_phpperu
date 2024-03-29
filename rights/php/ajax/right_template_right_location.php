<?php
namespace rights;

use user\UserDataManager;

use common\libraries\Utilities;
use common\libraries\Session;
use common\libraries\Authentication;

use rights\RightsUtilities;
/**
 * $Id: right_template_right_location.php 214 2009-11-13 13:57:37Z vanpouckesven $
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
// TODO: User real rights_templates'n'rights here
if (! $user->is_platform_admin())
{
    echo 0;
}

$rights = $_POST['rights'];
$rights = explode('_', $rights);

$right = $rights['1'];
$rights_template = $rights['2'];
$location = $rights['3'];

if (isset($rights_template) && isset($right) && isset($location))
{
    $success = RightsUtilities :: invert_rights_template_right_location($right, $rights_template, $location);
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