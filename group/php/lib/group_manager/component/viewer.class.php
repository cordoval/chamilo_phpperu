<?php
namespace group;
use common\libraries\Utilities;
use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\ToolbarItem; 
use common\libraries\Theme; 
use common\libraries\ActionBarSearchForm;  
use common\libraries\AdministrationComponent; 
use common\libraries\ActionBarRenderer;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;

use rights\RightsDatamanager;
 
require_once dirname(__FILE__) ."/../../group_rights.class.php";

/**
 * $Id: viewer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */
class GroupManagerViewerComponent extends GroupManager implements AdministrationComponent
{

    private $group;
    private $ab;
    private $root_group;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(GroupManager :: PARAM_GROUP_ID);
        if ($id)
        {
            $this->group = $this->retrieve_group($id);

            $this->root_group = $this->retrieve_groups(new EqualityCondition(Group :: PROPERTY_PARENT, 0))->next_result();

            $group = $this->group;

            if (!GroupRights::is_allowed_in_groups_subtree(GroupRights::RIGHT_VIEW, GroupRights::get_location_by_identifier_from_groups_subtree(Request::get(GroupManager::PARAM_GROUP_ID))))
            {
                Display :: not_allowed();
            }

            $this->display_header();
            $this->ab = $this->get_action_bar();
            echo $this->ab->as_html() . '<br />';

            echo '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_group.png);">';
            echo '<div class="title">' . Translation :: get('Details') . '</div>';
            echo '<b>' . Translation :: get('Code') . '</b>: ' . $group->get_code();
            echo '<br /><b>' . Translation :: get('Description') . '</b>: ' . $group->get_description();
            echo '<div class="clear">&nbsp;</div>';
            echo '</div>';

            $rdm = RightsDataManager :: get_instance();
            $group_rights_templates = $group->get_rights_templates();

            if ($group_rights_templates->size() > 0)
            {
                echo '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_rights.png);">';
                echo '<div class="title">' . Translation :: get('RightsTemplates') . '</div>';
                echo '<ul>';
                while ($group_rights_template = $group_rights_templates->next_result())
                {
                    $rights_template = $rdm->retrieve_rights_template($group_rights_template->get_rights_template_id());
                    echo '<li>' . $rights_template->get_name() . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }

            echo '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_users.png);">';
            echo '<div class="title">' . Translation :: get('Users') . '</div>';

            $parameters = $this->get_parameters();
            $parameters[GroupManager :: PARAM_GROUP_ID] = $id;
            $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
            $table = new GroupRelUserBrowserTable($this, $parameters, $this->get_condition());
            echo $table->as_html();
            echo '</div>';

            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }

    function get_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, Request :: get(GroupManager :: PARAM_GROUP_ID));

        $query = $this->ab->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*');
            $condition = new OrCondition($or_conditions);

            $users = UserDataManager :: get_instance()->retrieve_users($condition);
            while ($user = $users->next_result())
            {
                $userconditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, $user->get_id());
            }

            if (count($userconditions))
                $conditions[] = new OrCondition($userconditions);
            else
                $conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, 0);
        }

        $condition = new AndCondition($conditions);

        return $condition;
    }

    function get_action_bar()
    {
        $group = $this->group;

        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(GroupManager :: PARAM_GROUP_ID => $group->get_id())));

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(GroupManager :: PARAM_GROUP_ID => $group->get_id())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_group_editing_url($group), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if ($this->group != $this->root_group)
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_group_delete_url($group), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        if (GroupRights::is_allowed_in_groups_subtree(GroupRights::RIGHT_SUBSCRIBE, GroupRights::get_location_by_identifier_from_groups_subtree(Request::get(GroupManager::PARAM_GROUP_ID))))
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddUsers'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_group_suscribe_user_browser_url($group), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        if (GroupRights::is_allowed_in_groups_subtree(GroupRights::RIGHT_EDIT_RIGHTS, GroupRights::get_location_by_identifier_from_groups_subtree(Request::get(GroupManager::PARAM_GROUP_ID))))
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ManageRights'), Theme :: get_common_image_path() . 'action_rights.png', $this->get_group_edit_rights_url($group), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        $condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $group->get_id());
        $users = $this->retrieve_group_rel_users($condition);
        $visible = ($users->size() > 0);

        if (GroupRights::is_allowed_in_groups_subtree(GroupRights::RIGHT_UNSUBSCRIBE, GroupRights::get_location_by_identifier_from_groups_subtree(Request::get(GroupManager::PARAM_GROUP_ID))))
        {
            if ($visible)
            {
                $toolbar_data[] = array('href' => $this->get_group_emptying_url($group), 'label' => Translation :: get('Truncate'), 'img' => Theme :: get_common_image_path() . 'action_recycle_bin.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);

                $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Truncate'), Theme :: get_common_image_path() . 'action_recycle_bin.png', $this->get_group_emptying_url($group), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
            else
            {
                $toolbar_data[] = array('label' => Translation :: get('TruncateNA'), 'img' => Theme :: get_common_image_path() . 'action_recycle_bin_na.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
                $action_bar->add_tool_action(new ToolbarItem(Translation :: get('TruncateNA'), Theme :: get_common_image_path() . 'action_recycle_bin_na.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }

        return $action_bar;
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('group general');
    }
    
    function get_additional_parameters()
    {
    	return array(GroupManager :: PARAM_GROUP_ID);
    }

}

?>