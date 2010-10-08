<?php
require_once dirname(__FILE__) . '/../../group_usage_tree_menu_data_provider.class.php';

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
        $this->get_user()->set_platformadmin(0);
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
        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = 'CONTENT GOES HERE';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
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