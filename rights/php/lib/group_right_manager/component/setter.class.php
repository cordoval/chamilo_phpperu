<?php
namespace rights;

use common\libraries\Translation;
use common\libraries\Request;

use rights\RightsUtilities;
/**
 * $Id: setter.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.group_right_manager.component
 */

class GroupRightManagerSetterComponent extends GroupRightManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $group = Request :: get('group_id');
        $right = Request :: get('right_id');
        $location_id = Request :: get(GroupRightManager :: PARAM_LOCATION);
        $location = $this->retrieve_location($location_id);

        if (isset($group) && isset($right) && isset($location))
        {
            $success = RightsUtilities :: invert_group_right_location($right, $group, $location->get_id());

            if ($location->get_parent() == 0)
            {
                $this->redirect(Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ($success == true ? false : true), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_GROUP_RIGHTS, GroupRightManager :: PARAM_GROUP_RIGHT_ACTION => GroupRightManager :: ACTION_BROWSE_GROUP_RIGHTS, GroupRightManager :: PARAM_SOURCE => $location->get_application(), GroupRightManager :: PARAM_GROUP => $group));
            }
            else
            {
                $this->redirect(Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ($success == true ? false : true), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_GROUP_RIGHTS, GroupRightManager :: PARAM_GROUP_RIGHT_ACTION => GroupRightManager :: ACTION_BROWSE_GROUP_RIGHTS, GroupRightManager :: PARAM_SOURCE => $location->get_application(), GroupRightManager :: PARAM_LOCATION => $location->get_parent(), GroupRightManager :: PARAM_GROUP => $group));
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

    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_GROUP_RIGHTS,
    															  GroupRightManager :: PARAM_GROUP_RIGHT_ACTION => GroupRightManager :: ACTION_BROWSE_GROUP_RIGHTS,
    															  GroupRightManager :: PARAM_SOURCE => Request :: get(GroupRightManager :: PARAM_SOURCE),
            													  GroupRightManager :: PARAM_LOCATION => $location_id,
            													  GroupRightManager :: PARAM_GROUP => Request :: get(GroupRightManager :: PARAM_GROUP))),
    										 Translation :: get('RightsTemplateManagerConfigurerComponent')));
    	$breadcrumbtrail->add_help('rights_templates_setter');
    }

	function get_additional_parameters()
    {
    	return array(GroupRightManager :: PARAM_LOCATION, GroupRightManager :: PARAM_GROUP, 'right_id', GroupRightManager :: PARAM_SOURCE);
    }
}
?>