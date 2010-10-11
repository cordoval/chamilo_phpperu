<?php

require_once dirname(__FILE__) . '/context_template_table/table.class.php';

class SurveyContextManagerContextTemplateBrowserComponent extends SurveyContextManager
{
    private $ab;
    
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $trail = BreadcrumbTrail :: get_instance();
        
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseContextTemplate')));
        $this->ab = $this->get_action_bar();
        
        $output = $this->get_browser_html();
        
        $this->display_header();
        echo $this->ab->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_browser_html()
    {
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
        
        $table = new SurveyContextTemplateBrowserTable($this, $parameters, $this->get_condition());
        
        $html = array();
        $html[] = $table->as_html();
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_condition()
    {
        $condition = new EqualityCondition(SurveyContextTemplate :: PROPERTY_PARENT_ID, 1);
    	
    	$query = $this->ab->get_query();
                
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(SurveyContextTemplate :: PROPERTY_NAME, '*' . $query . '*', SurveyContextTemplate :: get_table_name());
            $search_conditions[] = new PatternMatchCondition(SurveyContextTemplate :: PROPERTY_DESCRIPTION, '*' . $query . '*', SurveyContextTemplate :: get_table_name());
            $or_condition = new OrCondition($search_conditions);
        
        }
        
        if($or_condition){
        	$conditions = array();
        	$conditions[] = $condition;
        	$conditions[] = $or_condition;
        	$condition = new AndCondition($conditions);
        }
        
        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
              
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_image_path() . 'action_add.png', $this->get_context_template_creation_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //		$action_bar->add_common_action ( new ToolbarItem ( Translation::get ( 'ViewRoot' ), Theme::get_common_image_path () . 'action_home.png', $this->get_browse_categories_url (), ToolbarItem::DISPLAY_ICON_AND_LABEL ) );
        //		$action_bar->add_common_action ( new ToolbarItem ( Translation::get ( 'ShowAll' ), Theme::get_common_image_path () . 'action_browser.png', $this->get_browse_categories_url (), ToolbarItem::DISPLAY_ICON_AND_LABEL ) );
        

        return $action_bar;
    }
}
?>