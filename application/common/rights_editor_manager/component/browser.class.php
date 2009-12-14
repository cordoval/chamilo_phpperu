<?php
/**
 * $Id: browser.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager.component
 */
require_once dirname(__FILE__) . '/location_user_browser/location_user_browser_table.class.php';
require_once dirname(__FILE__) . '/location_group_browser/location_group_browser_table.class.php';

class RightsEditorManagerBrowserComponent extends RightsEditorManagerComponent
{
    private $action_bar;
    private $type;
    
    const PARAM_TYPE = 're_type';
    const TYPE_USER = 'user';
    const TYPE_GROUP = 'group';

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->type = Request :: get(self :: PARAM_TYPE);
        if (! $this->type)
            $this->type = self :: TYPE_USER;
        
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(array(RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => RightsEditorManager :: ACTION_BROWSE_RIGHTS)), Translation :: get('BrowseRights')));
        
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header($trail);
        
        $this->display_type_selector();
        
        $html = array();
        $html[] = $this->action_bar->as_html() . '<br />';
        
        if ($this->type == self :: TYPE_USER)
        {
            $table = new LocationUserBrowserTable($this, $this->get_parameters(), $this->get_condition());
            $html[] = $table->as_html();
            $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'rights/javascript/configure_user.js');
        }
        else
        {
            $table = new LocationGroupBrowserTable($this, $this->get_parameters(), $this->get_condition());
            $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
            
            $group = Request :: get(RightsEditorManager :: PARAM_GROUP);
            
            $group_menu = new GroupMenu($group, 'core.php?go=rights&application=repository&category=' . Request :: get('category') . '&re_type=group&object=' . Request :: get('object') . '&group=%s');
            $html[] = $group_menu->render_as_tree();
            
            $html[] = '</div>';
            $html[] = '<div style="float: right; width: 80%;">';
            $html[] = $table->as_html();
            $html[] = '</div>';
            $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'rights/javascript/configure_group.js');
        }
        
        $html[] = RightsUtilities :: get_rights_legend();
        
        echo implode("\n", $html);
        
        $this->display_footer();
    }

    function display_type_selector()
    {
        $html = array();
        
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
        
        echo implode("\n", $html);
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            if ($this->type == self :: TYPE_USER)
            {
                $conditions = array();
                $conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, $query);
                $conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, $query);
                $conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, $query);
                $condition = new OrCondition($conditions);
            }
            else
            {
                $condition = new PatternMatchCondition(Group :: PROPERTY_NAME, $query);
            }
        }
        
        if ($this->type == self :: TYPE_GROUP)
        {
            $group = Request :: get(RightsEditorManager :: PARAM_GROUP) ? Request :: get(RightsEditorManager :: PARAM_GROUP) : 0;
            $parent_condition = new EqualityCondition(Group :: PROPERTY_PARENT, $group);
            if ($condition)
            {
                $conditions = array();
                $conditions[] = $condition;
                $conditions[] = $parent_condition;
                $condition = new AndCondition($conditions);
            }
            else
            {
                $condition = $parent_condition;
            }
            
        	if(count($this->get_excluded_groups()) > 0)
        	{
        		foreach($this->get_excluded_groups() as $group)
        		{
        			$conditions[] = new NotCondition(new EqualityCondition(Group :: PROPERTY_ID, $group));
        		}
        		
        		$conditions[] = $condition;
        		$condition = new AndCondition($conditions);
        	}
            
        }
        else
        {
        	if(count($this->get_excluded_users()) > 0)
        	{
        		foreach($this->get_excluded_users() as $user)
        		{
        			$conditions[] = new NotCondition(new EqualityCondition(User :: PROPERTY_ID, $user));
        		}
        		
        		if($condition)
        			$conditions[] = $condition;
        		$condition = new AndCondition($conditions);
        	}
        }
        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }

}
?>