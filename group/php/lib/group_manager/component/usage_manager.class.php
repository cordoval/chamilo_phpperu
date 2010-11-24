<?php
namespace group;

use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\AdministrationComponent;
use common\libraries\TreeMenu;
use common\libraries\Request;
use common\libraries\EqualityCondition;

require_once dirname(__FILE__) . '/../../group_usage_tree_menu_data_provider.class.php';
require_once dirname(__FILE__) . '/../../forms/group_usage_form.class.php';

class GroupManagerUsageManagerComponent extends GroupManager implements AdministrationComponent
{
    /**
     * @var integer
     */
    private $group;

    /**
     * @var Group
     */
    private $root_group;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $menu = $this->get_menu_html();
        $output = $this->get_user_html();

        $this->display_header();
        echo $menu;
        echo $output;
        $this->display_footer();
    }

    function get_menu_html()
    {
        //        $this->get_user()->set_platformadmin(1);
        $group_menu = new TreeMenu('GroupUsageTreeMenu', new GroupUsageTreeMenuDataProvider($this->get_user(), $this->get_url(), $this->get_group()));
        $html = array();
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $group_menu->render_as_tree();
        $html[] = '</div>';
        return implode($html, "\n");
    }

    function get_user_html()
    {
        $html = array();

        if (in_array($this->get_group(), $this->get_user()->get_allowed_groups()) || $this->get_user()->is_platform_admin())
        {
            $usage_form = new GroupUsageForm($this->get_url(), $this->get_group(), $this->get_user());

            if ($usage_form->validate())
            {
                $success = $usage_form->update_group_usage();
                $message = $success ? Translation :: get('ObjectUpdated', array('OBJECT' => Translation :: get('GroupUsage')) , Utilities :: COMMON_LIBRARIES) : Translation :: get('ObjectNotUpdated', array('OBJECT' => Translation :: get('GroupUsage')) , Utilities :: COMMON_LIBRARIES);
                $this->redirect($message, !$success, array(GroupManager :: PARAM_GROUP_ID => $this->get_group()));
            }
            else
            {
                $html[] = '<div style="float: right; width: 80%;">';
                $html[] = $usage_form->toHtml();
                $html[] = '</div>';
                $html[] = '<div class="clear"></div>';
            }
        }
        else
        {
            $html[] = '<div style="float: right; width: 80%;">';
            if ($this->get_group() == $this->get_root_group()->get_id())
            {
                $html[] = Display :: warning_message(Translation :: get('NoPermissionsToConfigureRoot'), true);
            }
            else
            {
                $html[] = Display :: error_message('NoPermissionsToConfigureGroupUsage', true);
            }
            $html[] = '</div>';
            $html[] = '<div class="clear"></div>';
        }

        return implode($html, "\n");
    }

    function get_group()
    {
        if (! $this->group)
        {
            $this->group = Request :: get(GroupManager :: PARAM_GROUP_ID);

            if (! $this->group)
            {
                $this->group = $this->get_root_group()->get_id();
            }

        }

        return $this->group;
    }

    function get_root_group()
    {
        if (! $this->root_group)
        {
            $group = $this->retrieve_groups(new EqualityCondition(Group :: PROPERTY_PARENT, 0))->next_result();
            $this->root_group = $group;
        }

        return $this->root_group;
    }
}
?>