<?php
/**
 * $Id: user_right_location.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.ajax
 */
$this_section = 'rights';

require_once dirname(__FILE__) . '/../../../../../common/global.inc.php';

Translation :: set_application($this_section);
Theme :: set_application($this_section);

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

$locations = json_decode(Request :: post('locations'));

$rights = $_POST['rights'];
$rights = explode('_', $rights);

$right = $rights['1'];
$right_user = $rights['2'];

if (isset($right_user) && isset($right) && isset($locations) && count($locations) > 0)
{
    $success = true;
    
    foreach($locations as $location)
    {
    	$success &= RightsUtilities :: invert_user_right_location($right, $right_user, $location);
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
