<?php
require_once dirname(__FILE__) . '/../survey_manager.class.php';

//require_once dirname(__FILE__) . '/../../survey_publication_category_menu.class.php';
require_once dirname(__FILE__) . '/survey_publication_browser/survey_publication_browser_table.class.php';

class SurveyManagerBrowserComponent extends SurveyManager
{
    private $action_bar;

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        //        $trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
        

        $this->action_bar = $this->get_action_bar();
        //        $menu = $this->get_menu();
        

        $this->display_header($trail);
        
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        //        echo '<div style="float: left; width: 17%; overflow: auto;">';
        //        echo $menu->render_as_tree();
        //        echo '</div>';
        //        echo '<div style="width: 80%; float: right;">';
//        echo '<div >' . Translation :: get('TakeSurveyExplanation') . '<br/>';
        //        echo '</div>';
        echo $this->get_tables();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

//    function get_table()
//    {
//        $table = new SurveyPublicationBrowserTable($this, array(Application :: PARAM_APPLICATION => SurveyManager :: APPLICATION_NAME, Application :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS), $this->get_condition());
//        return $table->as_html();
//    }

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

//    function get_menu()
//    {
//        $current_category = Request :: get(SurveyPublicationCategoryMenu :: PARAM_CATEGORY);
//        $current_category = $current_category ? $current_category : 0;
//        $menu = new SurveyPublicationCategoryMenu($current_category);
//        return $menu;
//    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
        
        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: ADD_RIGHT, 'publication_browser', SurveyRights :: TYPE_SURVEY_COMPONENT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_create_survey_publication_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        //        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: VIEW_RIGHT, 'category_manager', SurveyRights :: TYPE_SURVEY_COMPONENT))
        //        {
        //            $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_manage_survey_publication_categories_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //        }
        

//        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: VIEW_RIGHT, 'testcase_browser', SurveyRights :: TYPE_SURVEY_COMPONENT))
//        {
//            $action_bar->add_common_action(new ToolbarItem(Translation :: get('TestSurveys'), Theme :: get_common_image_path() . 'action_category.png', $this->get_testcase_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
//        }
        
        //         $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Test'), Theme :: get_common_image_path() . 'action_view_results.png', $this->get_survey_results_viewer_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ImportSurvey'), Theme :: get_common_image_path() . 'action_import.png', $this->get_import_survey_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        

        return $action_bar;
    }

    function get_condition($type)
    {
        //        $current_category = Request :: get(SurveyPublicationCategoryMenu :: PARAM_CATEGORY);
        //        $current_category = $current_category ? $current_category : 0;
        

        $query = $this->action_bar->get_query();
        
        $user = $this->get_user();
        $datamanager = SurveyDataManager :: get_instance();
        
        $publication_alias = SurveyPublication :: get_table_name();
        
        //        SurveyDataManager::get_instance()->get_alias(SurveyPublication :: get_table_name());
        

        $conditions = array();
     
        
        $conditions[] = new EqualityCondition(SurveyPublication :: PROPERTY_TYPE, $type, $publication_alias);
        //        $conditions[] = new EqualityCondition(SurveyPublication :: PROPERTY_CATEGORY, $current_category, $publication_alias);
        

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
            
            $publication_group_alias = SurveyPublicationGroup :: get_table_name();
            $publication_user_alias = SurveyPublicationUser :: get_table_name();
            
            $user_id = $user->get_id();
            $groups = $user->get_groups(true);
            
            $access = array();
            $access[] = new InCondition(SurveyPublicationUser :: PROPERTY_USER, $user_id, $publication_user_alias);
            $access[] = new InCondition(SurveyPublicationGroup :: PROPERTY_GROUP_ID, $groups, $publication_group_alias);
            $access[] = new EqualityCondition(SurveyPublication :: PROPERTY_PUBLISHER, $user->get_id(), $publication_alias);
            $conditions[] = new OrCondition($access);
            
            $conditions[] = new EqualityCondition(SurveyPublication :: PROPERTY_HIDDEN, false, $publication_alias);
            
            $dates = array();
            $interval[] = new InequalityCondition(SurveyPublication :: PROPERTY_FROM_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, time(), $publication_alias);
            $interval[] = new InequalityCondition(SurveyPublication :: PROPERTY_TO_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, time(), $publication_alias);
            $dates[] = new AndCondition($interval);
            $dates[] = new AndCondition(array(new EqualityCondition(SurveyPublication :: PROPERTY_FROM_DATE, 0, $publication_alias), new EqualityCondition(SurveyPublication :: PROPERTY_TO_DATE, 0, $publication_alias)));
            $conditions[] = new OrCondition($dates);
            
            return new AndCondition($conditions);
        }
    
    }
}
?>