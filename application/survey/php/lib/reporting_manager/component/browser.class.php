<?php namespace application\survey;

require_once dirname(__FILE__) . '/reporting_template_table/table.class.php';
require_once dirname(__FILE__) . '/publication_rel_reporting_template_table/table.class.php';
require_once Path :: get_application_path() . 'lib/survey/survey_publication_rel_reporting_template_registration.class.php';

class SurveyReportingManagerBrowserComponent extends SurveyReportingManager
{
    
    const TAB_PUBLICATION_REL_TEMPLATE_REGISTRATIONS = 1;
    const TAB_TEMPLATE_REGISTRATIONS = 2;
    
    private $action_bar;
    private $publication_id;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_VIEW, SurveyRights :: LOCATION_REPORTING, SurveyRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $this->publication_ids = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
        
        if (! empty($this->publication_ids))
        {
            if (! is_array($this->publication_ids))
            {
                $this->publication_ids = array($this->publication_ids);
            }
            
            $this->action_bar = $this->get_action_bar();
            
            $output = $this->get_tabs_html();
            
            $this->display_header();
            echo $this->action_bar->as_html() . '<br />';
            echo $output;
            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoSurveyPublicationsSelected')));
        }
    }

    function get_tabs_html()
    {
        
        $html = array();
        
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_PUBLICATION_REL_TEMPLATE_REGISTRATIONS;
        $table = new SurveyPublicationRelReportingTemplateTable($this, $parameters, $this->get_publication_rel_template_registration_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_PUBLICATION_REL_TEMPLATE_REGISTRATIONS, Translation :: get('ReportingTemplates'), Theme :: get_image_path('survey') . 'place_mini_survey.png', $table->as_html()));
        
        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_ADD_REPORTING_TEMPLATE, $this->publication_id, SurveyRights :: TYPE_PUBLICATION))
        {
            $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_TEMPLATE_REGISTRATIONS;
            $table = new SurveyReportingTemplateTable($this, $parameters, $this->get_template_registration_condition());
            $tabs->add_tab(new DynamicContentTab(self :: TAB_TEMPLATE_REGISTRATIONS, Translation :: get('AddReportingTemplates'), Theme :: get_image_path('survey') . 'place_mini_survey.png', $table->as_html()));
        }
        
        $html[] = $tabs->render();
        
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_template_registration_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_APPLICATION, SurveyManager :: APPLICATION_NAME);
        
        $reporting_template_registration_ids = array();
        $condition = new InCondition(SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_PUBLICATION_ID, $this->publication_ids);
        
        $publication_rel_reporting_template_registrations = SurveyDataManager :: get_instance()->retrieve_survey_publication_rel_reporting_template_registrations($condition);
        while ($publication_rel_reporting_template_registration = $publication_rel_reporting_template_registrations->next_result())
        {
            if ($publication_rel_reporting_template_registration->get_level() == 0)
            {
                $reporting_template_registration_ids[] = $publication_rel_reporting_template_registration->get_reporting_template_registration_id();
            }
        }
        if (count($reporting_template_registration_ids))
        {
            $in_condition = new InCondition(ReportingTemplateRegistration :: PROPERTY_ID, $reporting_template_registration_ids);
            $conditions[] = new NotCondition($in_condition);
        }
        
        //        $query = $this->action_bar->get_query();
        //        if (isset($query) && $query != '')
        //        {
        //            $or_conditions = array();
        //            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_CITY_NAME, '*' . $query . '*');
        //            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, '*' . $query . '*');
        //            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_DESCRIPTION, '*' . $query . '*');
        //            $or_condition = new OrCondition($or_conditions);
        //            
        //            $and_conditions = array();
        //            $and_conditions[] = $condition;
        //            $and_conditions[] = $or_condition;
        //            $condition = new AndCondition($and_conditions);
        //        }
        

        return $condition = new AndCondition($conditions);
    }

    function get_publication_rel_template_registration_condition()
    {
        $condition = new InCondition(SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_PUBLICATION_ID, $this->publication_ids);
        
        //        $query = $this->action_bar->get_query();
        //        if (isset($query) && $query != '')
        //        {
        //            $or_conditions = array();
        //            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_CITY_NAME, '*' . $query . '*');
        //            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, '*' . $query . '*');
        //            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_DESCRIPTION, '*' . $query . '*');
        //            $or_condition = new OrCondition($or_conditions);
        //            
        //            $and_conditions = array();
        //            $and_conditions[] = $condition;
        //            $and_conditions[] = $or_condition;
        //            $condition = new AndCondition($and_conditions);
        //        }
        

        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
        
        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_ACTIVATE, SurveyRights :: LOCATION_REPORTING, SurveyRights :: TYPE_COMPONENT))
        {
            //            $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerRegion'), Theme :: get_common_image_path() . 'action_create.png', $this->get_region_create_url($this->get_region()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        return $action_bar;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE)), Translation :: get('BrowseSurveys')));
    }

    function get_additional_parameters()
    {
        return array(SurveyManager :: PARAM_PUBLICATION_ID);
    }

}
?>