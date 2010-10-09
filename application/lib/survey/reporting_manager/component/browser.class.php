<?php
require_once dirname(__FILE__) . '/../region_manager.class.php';
require_once dirname(__FILE__) . '/browser/browser_table.class.php';

class SurveyReportingManagerBrowserComponent extends SurveyReportingManager
{
    
    const TAB_TEMPLATE_REGISTRATIONS = 1;
       
    private $action_bar;
    private $region;
    private $parent_region;
    private $root_region;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! SurveyRights :: is_allowed_in_internship_organizers_subtree(SurveyRights :: RIGHT_VIEW, SurveyRights :: LOCATION_REPORTING, SurveyRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $this->action_bar = $this->get_action_bar();

        $output = $this->get_tabs_html();
        
        $this->display_header();
        echo $this->action_bar->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_tabs_html()
    {
        
        $html = array();
        
        
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        
        // Subregion table tab
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_TEMPLATE_REGISTRATIONS;
        $table = new SurveyReportingTemplateTable($this, $parameters, $this->get_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_TEMPLATE_REGISTRATIONS, Translation :: get('ReportingTemplates'), Theme :: get_image_path('survey') . 'place_mini_survey.png', $table->as_html()));
        
//        $tabs->add_tab(new DynamicContentTab(self :: TAB_DETAIL, Translation :: get('Detail'), Theme :: get_image_path('survey') . 'place_mini_survey.png', $this->get_region_info()));
        
        $html[] = $tabs->render();
        
       
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

//    function get_region_info()
//    {
//        
//        $region = $this->retrieve_region($this->get_region());
//        
//        $html = array();
//        
//        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
//        
//        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_REGION, InternshipOrganizerRights :: TYPE_COMPONENT))
//        {
//            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_region_editing_url($region), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
//        }
//        if ($region->get_parent_id() != 0)
//        {
//            if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, InternshipOrganizerRights :: LOCATION_REGION, InternshipOrganizerRights :: TYPE_COMPONENT))
//            {
//                $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_region_delete_url($region), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
//            }
//        }
//        
//        $html[] = '<b>' . Translation :: get('ZipCode') . '</b>: ' . $region->get_zip_code() . '<br />';
//        $html[] = '<b>' . Translation :: get('City') . '</b>: ' . $region->get_city_name() . '<br />';
//        
//        $description = $region->get_description();
//        if ($description)
//        {
//            $html[] = '<b>' . Translation :: get('Description') . '</b>: ' . $description . '<br />';
//        }
//        
//        $html[] = '<br />';
//        $html[] = $toolbar->as_html();
//        
//        return implode("\n", $html);
//    }


    function get_condition()
    {
        $condition = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_APPLICATION, SurveyManager :: APPLICATION_NAME);
        
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
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ViewRoot'), Theme :: get_common_image_path() . 'action_home.png', $this->get_browse_regions_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(SurveyRights :: RIGHT_ACTIVATE, SurveyRights :: LOCATION_REPORTING, SurveyRights :: TYPE_COMPONENT))
        {
//            $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerRegion'), Theme :: get_common_image_path() . 'action_create.png', $this->get_region_create_url($this->get_region()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
               
        return $action_bar;
    }
}
?>