<?php
require_once dirname(__FILE__) ."/../../group_rights.class.php";
/**
 * $Id: truncater.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class GroupManagerTruncaterComponent extends GroupManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();

        if (!GroupRights::is_allowed_in_groups_subtree(GroupRights::RIGHT_UNSUBSCRIBE, GroupRights::get_location_by_identifier_from_groups_subtree(Request::get(GroupManager::PARAM_GROUP_ID))))
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }

        $ids = Request :: get(GroupManager :: PARAM_GROUP_ID);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $group = $this->retrieve_group($id);
                if (! $group->truncate())
                {
                    $failures ++;
                }
                else
                {
                    Event :: trigger('empty', GroupManager :: APPLICATION_NAME, array(ChangesTracker :: PROPERTY_REFERENCE_ID => $group->get_id(), ChangesTracker :: PROPERTY_USER_ID => $user->get_id()));
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedGroupNotEmptied';
                }
                else
                {
                    $message = 'SelectedGroupsNotEmptied';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedGroupEmptied';
                }
                else
                {
                    $message = 'SelectedGroupsEmptied';
                }

            }

            if (count($ids) == 1)
                $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $ids[0]));
            else
                $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoGroupSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupManagerBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => Request :: get(GroupManager :: PARAM_GROUP_ID))), Translation :: get('GroupManagerViewerComponent')));
    	$breadcrumbtrail->add_help('group general');
    }
    
    function get_additional_parameters()
    {
    	return array(GroupManager :: PARAM_GROUP_ID);
    }
}
?>