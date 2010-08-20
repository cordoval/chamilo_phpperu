<?php
/**
 * $Id: browser.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager.component
 */
require_once dirname(__FILE__) . '/location_user_browser/location_user_browser_table.class.php';
require_once dirname(__FILE__) . '/location_group_browser/location_group_browser_table.class.php';

class RightsEditorManagerBrowserComponent extends RightsEditorManager
{
    private $action_bar;
    private $type;
    
    const PARAM_TYPE = 'rights_type';
    const TYPE_USER = 'user';
    const TYPE_GROUP = 'group';
    
    const TAB_DETAILS = 0;
    const TAB_SUBGROUPS = 1;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->type = Request :: get(self :: PARAM_TYPE);
        $modus = $this->get_modus();
        if (! $this->type)
        {
            switch ($modus)
            {
                case RightsEditorManager :: MODUS_USERS :
                    $this->type = self :: TYPE_USER;
                    break;
                case RightsEditorManager :: MODUS_GROUPS :
                    $this->type = self :: TYPE_GROUP;
                    break;
                case RightsEditorManager :: MODUS_BOTH :
                    $this->type = self :: TYPE_USER;
                    break;
            }
        }
        elseif (($modus == RightsEditorManager :: MODUS_USERS && $this->type == self :: PARAM_GROUP) || ($modus == RightsEditorManager :: MODUS_GROUPS && $this->type == self :: TYPE_USER))
        {
            $this->not_allowed();
        }
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => RightsEditorManager :: ACTION_BROWSE_RIGHTS, self :: PARAM_TYPE => Request :: get(self :: PARAM_TYPE))), Translation :: get('BrowseRights')));
        
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header();
        
        $this->display_type_selector();
        
        $html = array();
        $html[] = $this->action_bar->as_html() . '<br />';
        
        $locations = array();
        
        foreach ($this->get_locations() as $location)
        {
            $locations[] = $location->get_id();
        }
        
        $html[] = '<script type="text/javascript">';
        //$html[] = '  var locations = \'{' . implode(',', $locations) . '}\';';
        $html[] = '  var application = \'' . Request :: get('application') . '\';';
        $html[] = '  var locations = \'' . json_encode($locations) . '\';';
        $html[] = '</script>';
        
        if ($this->type == self :: TYPE_USER)
        {
            $table = new LocationUserBrowserTable($this, $this->get_parameters(), $this->get_user_conditions());
            $html[] = $table->as_html();
            $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'application/common/rights_editor_manager/javascript/configure_user.js');
        }
        else
        {
            $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
            $tabs = new DynamicTabsRenderer($renderer_name);
            
            $html[] = '<div style="float: left; width: 18%; overflow: auto;">';
            
            $group = Request :: get(RightsEditorManager :: PARAM_GROUP) ? Request :: get(RightsEditorManager :: PARAM_GROUP) : 1;
            
            $url = $this->get_parent()->get_url(array(self :: PARAM_TYPE => 'group')) . '&group_id=%s';
            $group_menu = new GroupMenu($group, $url);
            $html[] = $group_menu->render_as_tree();
            
            $html[] = '</div>';
            $html[] = '<div style="float: right; width: 80%;">';
            
            $group_object = GroupDataManager :: get_instance()->retrieve_group($group);
            if ($group_object->has_children())
            {
                $table = new LocationGroupBrowserTable($this, $this->get_parameters(), $this->get_group_conditions());
                $tabs->add_tab(new DynamicContentTab(self :: TAB_SUBGROUPS, Translation :: get('Subgroups'), Theme :: get_image_path('admin') . 'place_mini_group.png', $table->as_html()));
            }
            
            $table = new LocationGroupBrowserTable($this, $this->get_parameters(), $this->get_group_conditions(false));
            $tabs->add_tab(new DynamicContentTab(self :: TAB_DETAILS, Translation :: get('Rights'), Theme :: get_image_path('admin') . 'place_mini_rights.png', $table->as_html()));
            
            $html[] = $tabs->render();
            $html[] = '</div>';
            $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'application/common/rights_editor_manager/javascript/configure_group.js');
        }
        
        $html[] = '<div class="clear"></div><br />';
        $html[] = RightsUtilities :: get_rights_legend();
        
        echo implode("\n", $html);
        
        $this->display_footer();
    }

    function display_type_selector()
    {
        $modus = $this->get_modus();
        
        $html = array();
        
        if ($modus == RightsEditorManager :: MODUS_BOTH)
        {
            $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/application.js');
            $html[] = '<div class="application_selecter">';
            
            $current = $this->type == self :: TYPE_USER ? ' current' : '';
            $html[] = '<a href="' . $this->get_url(array(self :: PARAM_TYPE => self :: TYPE_USER)) . '">';
            $html[] = '<div class="application' . $current . '" style="background-image: url(' . Theme :: get_image_path('admin') . 'place_user.png);">' . Translation :: get('Users') . '</div>';
            $html[] = '</a>';
            
            $current = $this->type == self :: TYPE_GROUP ? ' current' : '';
            $html[] = '<a href="' . $this->get_url(array(self :: PARAM_TYPE => self :: TYPE_GROUP)) . '">';
            $html[] = '<div class="application' . $current . '" style="background-image: url(' . Theme :: get_image_path('admin') . 'place_group.png);">' . Translation :: get('Groups') . '</div>';
            $html[] = '</a>';
            
            $html[] = '</div>';
            $html[] = '<div style="clear: both;"></div>';
        }
        
        echo implode("\n", $html);
    }

    function get_user_conditions()
    {
        $conditions = array();
        
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*');
            
            $conditions[] = new OrCondition($search_conditions);
        }
        
        if (count($this->get_limited_users()) > 0)
        {
            $conditions[] = new InCondition(User :: PROPERTY_ID, $this->get_limited_users());
        }
        
        if (count($this->get_excluded_users()) > 0)
        {
            $excluded_user_conditions = array();
            
            foreach ($this->get_excluded_users() as $user)
            {
                $excluded_user_conditions[] = new NotCondition(new EqualityCondition(User :: PROPERTY_ID, $user));
            }
            
            $conditions[] = new AndCondition($excluded_user_conditions);
        }
        
        return new AndCondition($conditions);
    }

    function get_group_conditions($get_children = true)
    {
        $conditions = array();
        
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(Group :: PROPERTY_NAME, '*' . $query . '*');
        }
        
        $group = Request :: get(RightsEditorManager :: PARAM_GROUP) ? Request :: get(RightsEditorManager :: PARAM_GROUP) : 1;
        if ($get_children)
        {
            $conditions[] = new EqualityCondition(Group :: PROPERTY_PARENT, $group);
        }
        else
        {
            $conditions[] = new EqualityCondition(Group :: PROPERTY_ID, $group);
        }
        
        if (count($this->get_limited_groups()) > 0)
        {
            $conditions[] = new InCondition(Group :: PROPERTY_ID, $this->get_limited_groups());
        }
        
        if (count($this->get_excluded_groups()) > 0)
        {
            $excluded_group_conditions = array();
            foreach ($this->get_excluded_groups() as $group)
            {
                $excluded_group_conditions[] = new NotCondition(new EqualityCondition(Group :: PROPERTY_ID, $group));
            }
            
            $conditions[] = new AndCondition($excluded_group_conditions);
        }
        
        return new AndCondition($conditions);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url(array(self :: PARAM_TYPE => $this->type)));
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(self :: PARAM_TYPE => $this->type)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        $locations = $this->get_locations();
        if(count($locations) == 1)
        {
        	$location = $locations[0];
        	$url = $this->get_url(array(RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => RightsEditorManager :: ACTION_CHANGE_INHERIT));
        	if($location->inherits())
        	{
        		$action_bar->add_common_action(new ToolbarItem(Translation :: get('NoInherit'), Theme :: get_common_image_path() . 'action_setting_false_inherit.png', $url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        	}
        	else
        	{
        		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Inherit'), Theme :: get_common_image_path() . 'action_setting_true_inherit.png', $url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        	}
        }
        
        return $action_bar;
    }

}
?>