<?php
/**
 * $Id: browser.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager.component
 */
require_once dirname(__FILE__) . '/location_user_browser/location_user_browser_table.class.php';
require_once dirname(__FILE__) . '/location_group_browser/location_group_browser_table.class.php';
require_once dirname(__FILE__) . '/location_template_browser/location_template_browser_table.class.php';

class RightsEditorManagerBrowserComponent extends RightsEditorManager
{
    protected $action_bar;
    protected $type;
    
    const PARAM_TYPE = 'rights_type';
    
    const TAB_DETAILS = 0;
    const TAB_SUBGROUPS = 1;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('rights_editor_browser');
        $this->set_parameter(self :: PARAM_TYPE, Request :: get(self :: PARAM_TYPE));
        $this->set_parameter(RightsEditorManager :: PARAM_GROUP, Request :: get(RightsEditorManager :: PARAM_GROUP));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('RightsEditorManagerBrowserComponent')));
        
    	$this->type = Request :: get(self :: PARAM_TYPE);
        if (!$this->type)
        {
            $allowed_types = $this->get_types();
            $this->type = $allowed_types[0];
        }
        else
        {
            if(!in_array($this->type, $this->get_types()))
            {
                $this->not_allowed();
            }
        }
        
        $trail = BreadcrumbTrail :: get_instance();
        //$trail->add(new Breadcrumb($this->get_url(array(RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => RightsEditorManager :: ACTION_BROWSE_RIGHTS, self :: PARAM_TYPE => Request :: get(self :: PARAM_TYPE))), Translation :: get('BrowseRights')));
        
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header();
        echo $this->get_display_html();
        $this->display_footer();
    }
    
    function get_display_html()
    {
    	$html = array();
        
        $html[] = $this->display_type_selector();
        $html[] = $this->action_bar->as_html() . '<br />';
        $html[] = $this->display_locations();
        
        if ($this->type == self :: TYPE_USER)
        {
            $html[] = $this->display_location_user_browser();
        }
        elseif($this->type == self :: TYPE_GROUP)
        {
            $html[] = $this->display_location_group_browser();
        }
        else
        {
            $html[] = $this->display_location_template_browser();
        }
        
        $html[] = '<div class="clear"></div><br />';
        $html[] = RightsUtilities :: get_rights_legend();
        
        return implode("\n", $html);
    }
    
    function display_locations()
    {
    	$html = array();
    	
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
        
        return implode("\n", $html);
    }
    
    function display_location_user_browser()
    {
    	$html = array();
    	
    	$parameters = $this->get_parameters();
        $parameters[self :: PARAM_TYPE] = self :: TYPE_USER;
        $parameters['query'] = $this->action_bar->get_query();
        $table = new LocationUserBrowserTable($this, $parameters, $this->get_user_conditions());
        $html[] = '<div style="overflow: auto;">';
        $html[] = $table->as_html();
        $html[] = '</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'application/common/rights_editor_manager/javascript/configure_user.js');
        
        return implode("\n", $html);
    }
    
    function display_location_template_browser()
    {
    	$html = array();
    	
    	$parameters = $this->get_parameters();
        $parameters[self :: PARAM_TYPE] = self :: TYPE_TEMPLATE;
        $parameters['query'] = $this->action_bar->get_query();
        $table = new LocationTemplateBrowserTable($this, $parameters, $this->get_template_conditions());
        $html[] = '<div style="overflow: auto;">';
        $html[] = $table->as_html();
        $html[] = '</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'application/common/rights_editor_manager/javascript/configure_template.js');
        
        return implode("\n", $html);
    }
    
    function display_location_group_browser()
    {
    	$html = array();
    	
    	$renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
            
        $html[] = '<div style="float: left; width: 18%; overflow: auto;">';
            
        $group = Request :: get(RightsEditorManager :: PARAM_GROUP) ? Request :: get(RightsEditorManager :: PARAM_GROUP) : 1;
            
        $url = $this->get_parent()->get_url(array(self :: PARAM_TYPE => 'group')) . '&group_id=%s';
        $group_menu = new GroupMenu($group, $url);
        $html[] = $group_menu->render_as_tree();
            
        $html[] = '</div>';
        $html[] = '<div style="float: right; width: 80%; overflow:auto;">';
            
        $group_object = GroupDataManager :: get_instance()->retrieve_group($group);
        if ($group_object->has_children())
        {
            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_TYPE] = 'group';
            $parameters['query'] = $this->action_bar->get_query(); 
            $table = new LocationGroupBrowserTable($this, $parameters, $this->get_group_conditions());
            $tabs->add_tab(new DynamicContentTab(self :: TAB_SUBGROUPS, Translation :: get('Subgroups'), Theme :: get_image_path('admin') . 'place_mini_group.png', $table->as_html()));
        }
            
        $table = new LocationGroupBrowserTable($this, $this->get_parameters(), $this->get_group_conditions(false));
        $tabs->add_tab(new DynamicContentTab(self :: TAB_DETAILS, Translation :: get('Rights'), Theme :: get_image_path('admin') . 'place_mini_rights.png', $table->as_html()));
            
        $html[] = $tabs->render();
        $html[] = '</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'application/common/rights_editor_manager/javascript/configure_group.js');
        
        return implode("\n", $html);
    }

    function display_type_selector()
    {
        $types = $this->get_types();
        
        $html = array();
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/application.js');
        $html[] = '<div class="application_selecter">';
            
        if (in_array(self :: TYPE_USER, $types))
        {
            $current = $this->type == self :: TYPE_USER ? ' current' : '';
            $html[] = '<a href="' . $this->get_url(array(self :: PARAM_TYPE => self :: TYPE_USER)) . '">';
            $html[] = '<div class="application' . $current . '" style="background-image: url(' . Theme :: get_image_path('admin') . 'place_user.png);">' . Translation :: get('Users') . '</div>';
            $html[] = '</a>';
        }
        
        if (in_array(self :: TYPE_GROUP, $types))
        {
            $current = $this->type == self :: TYPE_GROUP ? ' current' : '';
            $html[] = '<a href="' . $this->get_url(array(self :: PARAM_TYPE => self :: TYPE_GROUP)) . '">';
            $html[] = '<div class="application' . $current . '" style="background-image: url(' . Theme :: get_image_path('admin') . 'place_group.png);">' . Translation :: get('Groups') . '</div>';
            $html[] = '</a>';
        }
        
        if (in_array(self :: TYPE_TEMPLATE, $types))
        {
            $current = $this->type == self :: TYPE_TEMPLATE ? ' current' : '';
            $html[] = '<a href="' . $this->get_url(array(self :: PARAM_TYPE => self :: TYPE_TEMPLATE)) . '">';
            $html[] = '<div class="application' . $current . '" style="background-image: url(' . Theme :: get_image_path('admin') . 'place_template.png);">' . Translation :: get('Templates') . '</div>';
            $html[] = '</a>';
        }
        
        $html[] = '</div>';
        $html[] = '<div style="clear: both;"></div>';
        
        return implode("\n", $html);
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
    
    function get_template_conditions()
    {
        $conditions = array();
        
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(RightsTemplate :: PROPERTY_NAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(RightsTemplate :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $conditions[] = new OrCondition($search_conditions);
        }
        
        if (count($this->get_limited_templates()) > 0)
        {
            $conditions[] = new InCondition(RightsTemplate :: PROPERTY_ID, $this->get_limited_templates());
        }
        
        if (count($this->get_excluded_templates()) > 0)
        {
            $excluded_template_conditions = array();
            
            foreach ($this->get_excluded_templates() as $template)
            {
                $excluded_template_conditions[] = new NotCondition(new EqualityCondition(RightsTemplate :: PROPERTY_ID, $template));
            }
            
            $conditions[] = new AndCondition($excluded_template_conditions);
        }
        
        if(count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }
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
        	
        	if($location->get_parent())
        	{
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
        }
        
        return $action_bar;
    }
    function  get_additional_parameters()
    {
        return array();
    }

}
?>