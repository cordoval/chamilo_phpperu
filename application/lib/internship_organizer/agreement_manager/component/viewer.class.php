<?php

require_once dirname(__FILE__) . '/../agreement_manager.class.php';

//require_once dirname ( __FILE__ ) . '/rel_moment_browser/rel_moment_browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/moment_browser/browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/rel_location_browser/rel_location_browser_table.class.php';

class InternshipOrganizerAgreementManagerViewerComponent extends InternshipOrganizerAgreementManager
{
    
    const TAB_LOCATIONS = 0;
    const TAB_MOMENTS = 1;
    const TAB_COORDINATOR = 2;
    const TAB_COACH = 3;
    const TAB_MENTOR = 4;
    const TAB_PUBLICATIONS = 5;
    
    private $action_bar;
    private $agreement;

    function run()
    {
        
        $agreement_id = $_GET[InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID];
        $this->agreement = $this->retrieve_agreement($agreement_id);
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipOrganizerAgreements')));
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id)), $this->agreement->get_name()));
        
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header($trail);
        
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        
        echo '<div>';
        echo $this->get_tabs();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

    function get_tabs()
    {
        
        $html[] = array();
        $html[] = '<div>';
        
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $count = $this->count_agreement_rel_locations($this->get_location_condition(InternshipOrganizerAgreementRelLocation :: APPROVED));
        if ($count == 1)
        {
            //the agreement is aproved so it is possible to add moments and create publications
            $parameters = $this->get_parameters();
            $parameters[InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID] = $this->agreement->get_id();
            $table = new InternshipOrganizerMomentBrowserTable($this, $parameters, $this->get_moment_condition());
            $tabs->add_tab(new DynamicContentTab(self :: TAB_MOMENTS, Translation :: get('InternshipOrganizerMoments'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID] = $this->agreement->get_id();
            $table = new InternshipOrganizerAgreementRelLocationBrowserTable($this, $parameters, $this->get_location_condition(InternshipOrganizerAgreementRelLocation :: TO_APPROVE));
            $tabs->add_tab(new DynamicContentTab(self :: TAB_LOCATIONS, Translation :: get('InternshipOrganizerOrganisations'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        }
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $conditions = array();
        $agreement_id = $this->agreement->get_id();
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID, $agreement_id);
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_TYPE, InternshipOrganizerAgreementRelLocation :: APPROVED);
        $condition = new AndCondition($conditions);
        $count = $this->count_agreement_rel_locations($condition);
        if ($count == 1)
        {
            //all actions that you can do on a approved agreement
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerMoment'), Theme :: get_common_image_path() . 'action_create.png', $this->get_create_moment_url($this->agreement), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        }
        else
        {
            //add locations
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('SubscribeInternshipOrganizerAgreementLocation'), Theme :: get_common_image_path() . 'action_add.png', $this->get_subscribe_location_url($this->agreement), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        }
        
        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $this->agreement->get_id())));
        
        return $action_bar;
    }

    function get_moment_condition()
    {
        
        $query = $this->action_bar->get_query();
        $conditions = array();
        $agreement_id = $this->agreement->get_id();
        $conditions[] = new EqualityCondition(InternshipOrganizerMoment :: PROPERTY_AGREEMENT_ID, $agreement_id);
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMoment :: PROPERTY_NAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMoment :: PROPERTY_STREET, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMoment :: PROPERTY_STREET_NUMBER, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMoment :: PROPERTY_CITY, '*' . $query . '*');
            
            $conditions[] = new OrCondition($search_conditions);
        }
        return new AndCondition($conditions);
    }

    function get_location_condition($location_type)
    {
        
        $query = $this->action_bar->get_query();
        $conditions = array();
        $agreement_id = $this->agreement->get_id();
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID, $agreement_id);
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_TYPE, $location_type);
        
        //        if (isset($query) && $query != '')
        //        {
        //            $search_conditions = array();
        //            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMoment :: PROPERTY_NAME, '*' . $query . '*');
        //            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMoment :: PROPERTY_STREET, '*' . $query . '*');
        //            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMoment :: PROPERTY_STREET_NUMBER, '*' . $query . '*');
        //            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMoment :: PROPERTY_CITY, '*' . $query . '*');
        //            
        //            $conditions[] = new OrCondition($search_conditions);
        //        }
        return new AndCondition($conditions);
    }

}
?>