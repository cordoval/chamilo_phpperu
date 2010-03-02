<?php
/**
 * $Id: group_right_location.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.ajax
 */
$this_section = 'rights';

require_once dirname(__FILE__) . '/../../../../../common/global.inc.php';

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

$locations = Request :: post('locations');
//small fix for tags
$locations = str_replace('\\"', '"', $locations);
$locations = json_decode($locations);

$rights = $_POST['rights'];
$rights = explode('_', $rights);

$right = $rights['1'];
$right_group = $rights['2'];

if (isset($right_group) && isset($right) && isset($locations) && count($locations) > 0)
{
	$success = true;

	$rdm = RightsDataManager :: get_instance();

    foreach($locations as $location_id)
    {
    	$success &= RightsUtilities :: invert_group_right_location($right, $right_group, $location_id);

    	if(PlatformSetting :: get('use_cumulative_rights', 'repository'))
    	{
	    	$location = $rdm->retrieve_location($location_id);
	    	if($location->get_application() == 'repository' && $right >= RepositoryRights :: SEARCH_RIGHT)
	    	{
	    		$value = RightsUtilities :: get_group_right_location($right, $right_group, $location_id);
	    		if($value == 1)
	    		{
	    			for($i = RepositoryRights :: SEARCH_RIGHT; $i < $right; $i++)
	    			{
	    				RightsUtilities :: set_group_right_location_value($i, $right_group, $location_id, 1);
	    			}
	    		}
	    		else
	    		{
	    			for($i = $right + 1; $i <= RepositoryRights :: REUSE_RIGHT; $i++)
	    			{
	    				RightsUtilities :: set_group_right_location_value($i, $right_group, $location_id, 0);
	    			}
	    		}
	    	}
    	}
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
