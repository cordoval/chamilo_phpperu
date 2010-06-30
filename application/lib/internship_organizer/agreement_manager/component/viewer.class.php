<?php

require_once dirname(__FILE__) . '/../agreement_manager.class.php';

//require_once dirname ( __FILE__ ) . '/rel_moment_browser/rel_moment_browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/moment_browser/browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/rel_location_browser/rel_location_browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/user_browser/user_browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/rel_mentor_browser/rel_mentor_browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/publisher/publication_table/publication_table.class.php';

class InternshipOrganizerAgreementManagerViewerComponent extends InternshipOrganizerAgreementManager
{
    
    const TAB_LOCATIONS = 'loc_tab';
    const TAB_MOMENTS = 'mom_tab';
    const TAB_COORDINATOR = 'coord_tab';
    const TAB_COACH = 'coah_tab';
    const TAB_MENTOR = 'ment_tab';
    const TAB_PUBLICATIONS = 'pub_tab';
    
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
        
        $html = array();
        $html[] = '<div>';
        
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        //coordinators and coaches are added/checked when the agreement is created and so are always present
        $parameters = $this->get_parameters();
        $parameters[InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID] = $this->agreement->get_id();
        $table = new InternshipOrganizerAgreementUserBrowserTable($this, $parameters, $this->get_type_users_condition(InternshipOrganizerUserType :: COORDINATOR));
        $tabs->add_tab(new DynamicContentTab(self :: TAB_COORDINATOR, Translation :: get('InternshipOrganizerCoordinators'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $parameters = $this->get_parameters();
        $parameters[InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID] = $this->agreement->get_id();
        $table = new InternshipOrganizerAgreementUserBrowserTable($this, $parameters, $this->get_type_users_condition(InternshipOrganizerUserType :: COACH));
        $tabs->add_tab(new DynamicContentTab(self :: TAB_COACH, Translation :: get('InternshipOrganizerCoaches'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Publications table tab
        $table = new InternshipOrganizerPublicationTable($this, $parameters, $this->get_publications_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_PUBLICATIONS, Translation :: get('InternshipOrganizerPublications'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $count = $this->count_agreement_rel_locations($this->get_location_condition(InternshipOrganizerAgreementRelLocation :: APPROVED));
        if ($count == 1)
        {
            //the agreement is aproved for the chosen organisation/location
            

            $parameters = $this->get_parameters();
            $parameters[InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID] = $this->agreement->get_id();
            $table = new InternshipOrganizerAgreementRelLocationBrowserTable($this, $parameters, $this->get_location_condition(InternshipOrganizerAgreementRelLocation :: APPROVED));
            $tabs->add_tab(new DynamicContentTab(self :: TAB_LOCATIONS, Translation :: get('InternshipOrganizerOrganisations'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
            
            $parameters = $this->get_parameters();
            $parameters[InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID] = $this->agreement->get_id();
            $table = new InternshipOrganizerAgreementRelMentorBrowserTable($this, $parameters, $this->get_mentor_condition());
            $tabs->add_tab(new DynamicContentTab(self :: TAB_MENTOR, Translation :: get('InternshipOrganizerMentors'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
            
            $condition = new EqualityCondition(InternshipOrganizerAgreementRelMentor :: PROPERTY_AGREEMENT_ID, $this->agreement->get_id());
            $mentor_count = InternshipOrganizerDataManager :: get_instance()->count_agreement_rel_mentors($condition);
            if ($mentor_count > 0)
            {
                //the mentors are connencted so it is possible to create moments and view publications
                

                $parameters = $this->get_parameters();
                $parameters[InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID] = $this->agreement->get_id();
                $table = new InternshipOrganizerMomentBrowserTable($this, $parameters, $this->get_moment_condition());
                $tabs->add_tab(new DynamicContentTab(self :: TAB_MOMENTS, Translation :: get('InternshipOrganizerMoments'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
            }
        
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
        $location_count = $this->count_agreement_rel_locations($condition);
        if ($location_count == 1)
        {
            
            $condition = new EqualityCondition(InternshipOrganizerAgreementRelMentor :: PROPERTY_AGREEMENT_ID, $this->agreement->get_id());
            $mentor_count = InternshipOrganizerDataManager :: get_instance()->count_agreement_rel_mentors($condition);
            if ($mentor_count > 0)
            {
                //all actions that you can do on a approved agreement
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_moment_publish_url($this->agreement), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            	$action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerMoment'), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_moment_url($this->agreement), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                
            }
            else
            {
                //first mentors have to be added before moments can be created
                $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddInternshipOrganizerMentor'), Theme :: get_common_image_path() . 'action_add.png', $this->get_agreement_subscribe_mentor_url($this->agreement), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            }
        
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

    function get_mentor_condition()
    {
        
        $query = $this->action_bar->get_query();
        $conditions = array();
        $agreement_id = $this->agreement->get_id();
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelMentor :: PROPERTY_AGREEMENT_ID, $agreement_id);
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMentor :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMentor :: PROPERTY_LASTNAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMentor :: PROPERTY_TITLE, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMentor :: PROPERTY_TELEPHONE, '*' . $query . '*');
            
            $mentors = InternshipOrganizerDataManager :: get_instance()->retrieve_mentors($search_conditions);
            
            $mentor_ids = array();
            while ($mentor = $mentors->next_result())
            {
                $mentor_ids[] = $mentor->get_id();
            }
            
            if (count($mentor_ids))
            {
                
                $rel_mentor_condition = new InCondition(InternshipOrganizerAgreementRelMentor :: PROPERTY_MENTOR_ID, $mentor_ids);
            
            }
            else
            {
                $rel_mentor_condition = new EqualityCondition(InternshipOrganizerAgreementRelMentor :: PROPERTY_MENTOR_ID, 0);
            
            }
            
            $conditions[] = $rel_mentor_condition;
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

    function get_type_users_condition($user_type)
    {
        $query = $this->action_bar->get_query();
        $conditions = array();
        
        $user_ids = $this->agreement->get_user_ids($user_type);
        
        if (count($user_ids))
        {
            $conditions[] = new InCondition(User :: PROPERTY_ID, $user_ids);
        }
        else
        {
            $conditions[] = new EqualityCondition(User :: PROPERTY_ID, 0);
        }
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*');
            //            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*');
            $conditions[] = new OrCondition($search_conditions);
        }
        return new AndCondition($conditions);
    }

    function get_publications_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerPublication :: PROPERTY_PUBLICATION_PLACE, InternshipOrganizerPublicationPlace :: AGREEMENT);
        $conditions[] = new EqualityCondition(InternshipOrganizerPublication :: PROPERTY_PLACE_ID, $this->agreement->get_id());
        return new AndCondition($conditions);
    }

}
?>