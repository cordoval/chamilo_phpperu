<?php
require_once Path :: get_repository_path() . 'lib/external_repository_instance_manager/component/external_repository_instance_browser/external_repository_instance_browser_table.class.php';

class ExternalRepositoryInstanceManagerBrowserComponent extends ExternalRepositoryInstanceManager
{
    
    private $action_bar;

    function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            $this->not_allowed();
        }
        
        $this->action_bar = $this->get_action_bar();
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $table = new ExternalRepositoryInstanceBrowserTable($this, $parameters, $this->get_condition());
        
        $this->display_header();
        echo $this->action_bar->as_html();
        echo $table->as_html();
        $this->display_footer();
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        
        if (isset($query) && $query != '')
        {
            $conditions = array();
            $conditions[] = new PatternMatchCondition(ExternalRepository :: PROPERTY_TITLE, '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(ExternalRepository :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $condition = new AndCondition($conditions);
        }
        else
        {
            $condition = null;
        }
        
        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddExternalRepositoryInstance'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(ExternalRepositoryInstanceManager :: PARAM_INSTANCE_ACTION => ExternalRepositoryInstanceManager :: ACTION_CREATE_INSTANCE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        return $action_bar;
    }
}
?>