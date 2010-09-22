<?php
require_once dirname(__FILE__) . '/../survey_manager.class.php';

//require_once dirname(__FILE__) . '/../../survey_publication_category_menu.class.php';
require_once dirname(__FILE__) . '/publication_browser/publication_browser_table.class.php';

class SurveyManagerBrowserComponent extends SurveyManager
{
    private $action_bar;

    function run()
    {
        
        $this->action_bar = $this->get_action_bar();
  
        $this->display_header();
        
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';

        echo $this->get_tables();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

    function get_tables()
    {
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        
        $types = SurveyPublication :: get_types();
        
        foreach ($types as $type => $type_name)
        {
            $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = $type;
            $table = new SurveyPublicationBrowserTable($this, $parameters, $this->get_condition($type));
            $tabs->add_tab(new DynamicContentTab($type, Translation :: get($type_name), Theme :: get_image_path('survey') . 'survey-16.png', $table->as_html()));
        
        }
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        
        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_PUBLISH, 'publication_browser', SurveyRights :: TYPE_SURVEY_COMPONENT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_create_survey_publication_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
       

        return $action_bar;
    }

    function get_condition($type)
    {

    	$query = $this->action_bar->get_query();
        
        $user = $this->get_user();
        $datamanager = SurveyDataManager :: get_instance();
        
        $publication_alias = SurveyPublication :: get_table_name();
      
        $conditions = array();
        
        $conditions[] = new EqualityCondition(SurveyPublication :: PROPERTY_TYPE, $type, $publication_alias);
      

        if (isset($query) && $query != '')
        {
            
            $object_alias = ContentObject :: get_table_name();
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*', $object_alias);
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*', $object_alias);
            $subselect_condition = new OrCondition($search_conditions);
            $conditions[] = new SubselectCondition(SurveyPublication :: PROPERTY_CONTENT_OBJECT, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
        }
        
        if ($user->is_platform_admin())
        {
            return new AndCondition($conditions);
        }
        else
        {
            $conditions[] = new EqualityCondition(SurveyPublication :: PROPERTY_HIDDEN, false, $publication_alias);
            
            $dates = array();
            $interval[] = new InequalityCondition(SurveyPublication :: PROPERTY_FROM_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, time(), $publication_alias);
            $interval[] = new InequalityCondition(SurveyPublication :: PROPERTY_TO_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, time(), $publication_alias);
            $dates[] = new AndCondition($interval);
            $dates[] = new AndCondition(array(new EqualityCondition(SurveyPublication :: PROPERTY_FROM_DATE, 0, $publication_alias), new EqualityCondition(SurveyPublication :: PROPERTY_TO_DATE, 0, $publication_alias)));
            $dates[] = new EqualityCondition(SurveyPublication :: PROPERTY_PUBLISHER, $this->get_user_id(), $publication_alias);
            $conditions[] = new OrCondition($dates);
            
            return new AndCondition($conditions);
        }
    
    }

   

}
?>