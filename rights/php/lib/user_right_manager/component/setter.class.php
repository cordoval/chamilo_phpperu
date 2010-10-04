<?php
/**
 * $Id: setter.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.user_right_manager.component
 */

class UserRightManagerSetterComponent extends UserRightManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = Request :: get('user_id');
        $right = Request :: get('right_id');
        $location_id = Request :: get(UserRightManager :: PARAM_LOCATION);
        $location = $this->retrieve_location($location_id);
        
        if (isset($user) && isset($right) && isset($location))
        {
            $success = RightsUtilities :: invert_user_right_location($right, $user, $location->get_id());
            
            if ($location->get_parent() == 0)
            {
                $this->redirect(Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ($success == true ? false : true), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_USER_RIGHTS, UserRightManager :: PARAM_USER_RIGHT_ACTION => UserRightManager :: ACTION_BROWSE_USER_RIGHTS, UserRightManager :: PARAM_SOURCE => $location->get_application(), UserRightManager :: PARAM_USER => $user));
            }
            else
            {
                $this->redirect(Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ($success == true ? false : true), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_USER_RIGHTS, UserRightManager :: PARAM_USER_RIGHT_ACTION => UserRightManager :: ACTION_BROWSE_USER_RIGHTS, UserRightManager :: PARAM_SOURCE => $location->get_application(), UserRightManager :: PARAM_LOCATION => $location->get_parent(), UserRightManager :: PARAM_USER => $user));
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoLocationSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$ids = Request :: get(RightsTemplateManager :: PARAM_LOCATION);
    	$location_id = $ids[0];
    	
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_USER_RIGHTS,
    															  UserRightManager :: PARAM_USER_RIGHT_ACTION => UserRightManager :: ACTION_BROWSE_USER_RIGHTS,
    															  UserRightManager :: PARAM_SOURCE => Request :: get(UserRightManager :: PARAM_SOURCE), 
            													  UserRightManager :: PARAM_LOCATION => $location_id,
            													  UserRightManager :: PARAM_USER => Request :: get(UserRightManager :: PARAM_USER))), 
    										 Translation :: get('RightsTemplateManagerConfigurerComponent')));									
    	$breadcrumbtrail->add_help('rights_user_setter');
    }
    
	function get_additional_parameters()
    {
    	return array(UserRightManager :: PARAM_LOCATION, UserRightManager :: PARAM_USER, 'right_id', UserRightManager :: PARAM_SOURCE);
    }
}
?>