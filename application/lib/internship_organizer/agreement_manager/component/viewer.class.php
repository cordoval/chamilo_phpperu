<?php

require_once dirname(__FILE__) . '/../agreement_manager.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/moment_browser/browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/rel_location_browser/rel_location_browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/user_browser/user_browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/rel_mentor_browser/rel_mentor_browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/publisher/publication_table/publication_table.class.php';

class InternshipOrganizerAgreementManagerViewerComponent extends InternshipOrganizerAgreementManager
{
    
    const TAB_LOCATIONS = 1;
    const TAB_MOMENTS = 2;
    const TAB_COORDINATOR = 3;
    const TAB_COACH = 4;
    const TAB_MENTOR = 5;
    const TAB_PUBLICATIONS = 6;
    const TAB_EVALUATIONS = 7;
    
    private $action_bar;
    private $agreement;
    private $student;
    private $selected_tab;

    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_AGREEMENT, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $agreement_id = $_GET[self :: PARAM_AGREEMENT_ID];
        
        $this->agreement = $this->retrieve_agreement($agreement_id);
        
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header();
        
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        
        echo '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_location.png);">';
        echo '<div class="title">' . Translation :: get('Details') . '</div>';
        echo '<b>' . Translation :: get('Description') . '</b>: ' . $this->agreement->get_description() . '<br /> ';
        
        $student = $this->get_student();
        
        echo '<div class="title">' . Translation :: get('Student') . '</div>';
        echo '<b>' . Translation :: get('Firstname') . '</b>: ' . $student->get_firstname();
        echo '<br /><b>' . Translation :: get('Lastname') . '</b>: ' . $student->get_lastname();
        echo '<br /><b>' . Translation :: get('InternshipOrganizerEmail') . '</b>: ' . $student->get_email();
        
        echo '<div class="clear">&nbsp;</div>';
        echo '</div>';
        
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
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_COORDINATOR;
        $table = new InternshipOrganizerAgreementUserBrowserTable($this, $parameters, $this->get_type_users_condition(InternshipOrganizerUserType :: COORDINATOR));
        $tabs->add_tab(new DynamicContentTab(self :: TAB_COORDINATOR, Translation :: get('InternshipOrganizerCoordinators'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_COACH;
        $table = new InternshipOrganizerAgreementUserBrowserTable($this, $parameters, $this->get_type_users_condition(InternshipOrganizerUserType :: COACH));
        $tabs->add_tab(new DynamicContentTab(self :: TAB_COACH, Translation :: get('InternshipOrganizerCoaches'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Publications table tab
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_PUBLICATIONS;
        $table = new InternshipOrganizerPublicationTable($this, $parameters, $this->get_publications_condition(array(Document :: get_table_name())));
        $tabs->add_tab(new DynamicContentTab(self :: TAB_PUBLICATIONS, Translation :: get('InternshipOrganizerPublications'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        // Evaluations table tab
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_EVALUATIONS;
        $table = new InternshipOrganizerPublicationTable($this, $parameters, $this->get_publications_condition(array(Survey :: get_type_name())));
        $tabs->add_tab(new DynamicContentTab(self :: TAB_EVALUATIONS, Translation :: get('InternshipOrganizerEvaluations'), Theme :: get_image_path('internship_organizer') . 'place_mini_survey.png', $table->as_html()));
        
        $count = $this->count_agreement_rel_locations($this->get_location_condition(InternshipOrganizerAgreementRelLocation :: APPROVED));
        if ($count == 1)
        {
            //the agreement is aproved for the chosen organisation/location
            

            $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_LOCATIONS;
            $table = new InternshipOrganizerAgreementRelLocationBrowserTable($this, $parameters, $this->get_location_condition(InternshipOrganizerAgreementRelLocation :: APPROVED));
            $tabs->add_tab(new DynamicContentTab(self :: TAB_LOCATIONS, Translation :: get('InternshipOrganizerLocations'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
            
            $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_MENTOR;
            $table = new InternshipOrganizerAgreementRelMentorBrowserTable($this, $parameters, $this->get_mentor_condition());
            $tabs->add_tab(new DynamicContentTab(self :: TAB_MENTOR, Translation :: get('InternshipOrganizerMentors'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
            
            $condition = new EqualityCondition(InternshipOrganizerAgreementRelMentor :: PROPERTY_AGREEMENT_ID, $this->agreement->get_id());
            $mentor_count = InternshipOrganizerDataManager :: get_instance()->count_agreement_rel_mentors($condition);
            if ($mentor_count > 0)
            {
                //the mentors are connencted so it is possible to create moments and view publications
                

                $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_MOMENTS;
                $table = new InternshipOrganizerMomentBrowserTable($this, $parameters, $this->get_moment_condition());
                $tabs->add_tab(new DynamicContentTab(self :: TAB_MOMENTS, Translation :: get('InternshipOrganizerMoments'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
            }
        
        }
        else
        {
            $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_LOCATIONS;
            $table = new InternshipOrganizerAgreementRelLocationBrowserTable($this, $parameters, $this->get_location_condition(InternshipOrganizerAgreementRelLocation :: TO_APPROVE));
            $tabs->add_tab(new DynamicContentTab(self :: TAB_LOCATIONS, Translation :: get('InternshipOrganizerLocations'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
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
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_PUBLISH, $agreement_id, InternshipOrganizerRights :: TYPE_AGREEMENT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_agreement_publish_url($this->agreement), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        if ($location_count == 1)
        {
            
            $condition = new EqualityCondition(InternshipOrganizerAgreementRelMentor :: PROPERTY_AGREEMENT_ID, $this->agreement->get_id());
            $mentor_count = InternshipOrganizerDataManager :: get_instance()->count_agreement_rel_mentors($condition);
            if ($mentor_count > 0)
            {
                //all actions that you can do on a approved agreement
                

                if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_PUBLISH, $this->agreement->get_id(), InternshipOrganizerRights :: TYPE_AGREEMENT))
                {
                    $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishInMoments'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_moments_publish_url($this->agreement), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                }
                if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: ADD_MOMENT_RIGHT, $this->agreement->get_id(), InternshipOrganizerRights :: TYPE_AGREEMENT))
                {
                    $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerMoment'), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_moment_url($this->agreement), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                }
            }
            else
            {
                if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: ADD_MENTOR_RIGHT, $this->agreement->get_id(), InternshipOrganizerRights :: TYPE_AGREEMENT))
                {
                    //first mentors have to be added before moments can be created
                    $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddInternshipOrganizerMentor'), Theme :: get_common_image_path() . 'action_add.png', $this->get_agreement_subscribe_mentor_url($this->agreement), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                }
            }
        
        }
        else
        {
            
            if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: ADD_LOCATION_RIGHT, $this->agreement->get_id(), InternshipOrganizerRights :: TYPE_AGREEMENT))
            {
                //add locations
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('SubscribeInternshipOrganizerAgreementLocation'), Theme :: get_common_image_path() . 'action_add.png', $this->get_subscribe_location_url($this->agreement), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
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
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMoment :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            //            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMoment :: PROPERTY_STREET_NUMBER, '*' . $query . '*');
            //            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMoment :: PROPERTY_CITY, '*' . $query . '*');
            

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
        
        //        if (isset($query) && $query != '')
        //        {
        //            $search_conditions = array();
        //            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMentor :: PROPERTY_FIRSTNAME, '*' . $query . '*');
        //            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMentor :: PROPERTY_LASTNAME, '*' . $query . '*');
        //            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMentor :: PROPERTY_TITLE, '*' . $query . '*');
        //            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMentor :: PROPERTY_TELEPHONE, '*' . $query . '*');
        //            
        //            $mentors = InternshipOrganizerDataManager :: get_instance()->retrieve_mentors($search_conditions);
        //            
        //            $mentor_ids = array();
        //            while ($mentor = $mentors->next_result())
        //            {
        //                $mentor_ids[] = $mentor->get_id();
        //            }
        //            
        //            if (count($mentor_ids))
        //            {
        //                
        //                $rel_mentor_condition = new InCondition(InternshipOrganizerAgreementRelMentor :: PROPERTY_MENTOR_ID, $mentor_ids);
        //            
        //            }
        //            else
        //            {
        //                $rel_mentor_condition = new EqualityCondition(InternshipOrganizerAgreementRelMentor :: PROPERTY_MENTOR_ID, 0);
        //            
        //            }
        //            
        //            $conditions[] = $rel_mentor_condition;
        //        }
        return new AndCondition($conditions);
    }

    function get_location_condition($location_type)
    {
        $conditions = array();
        $agreement_id = $this->agreement->get_id();
        
        $agreement_rel_location_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerAgreementRelLocation :: get_table_name());
        
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID, $agreement_id, $agreement_rel_location_alias, true);
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_TYPE, $location_type, $agreement_rel_location_alias, true);
        
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            
            $region_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerRegion :: get_table_name());
            $organisation_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerOrganisation :: get_table_name());
            $location_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerLocation :: get_table_name());
            
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_ADDRESS, '*' . $query . '*', $location_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_DESCRIPTION, '*' . $query . '*', $location_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_EMAIL, '*' . $query . '*', $location_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_NAME, '*' . $query . '*', $location_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_TELEPHONE, '*' . $query . '*', $location_alias, true);
                      
            $conditions[] = new OrCondition($search_conditions);
        }
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
            $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
            
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*', $user_alias, true);
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*', $user_alias, true);
            //            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*');
            $conditions[] = new OrCondition($search_conditions);
        }
        return new AndCondition($conditions);
    }

    function get_student()
    {
        $student_id = $this->agreement->get_user_ids(InternshipOrganizerUserType :: STUDENT);
        
        $dm = UserDataManager :: get_instance();
        
        $student = $dm->retrieve_user($student_id[0]);
        return $student;
    
    }

    function get_publications_condition($types = array(''))
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerPublication :: PROPERTY_PUBLICATION_PLACE, InternshipOrganizerPublicationPlace :: AGREEMENT);
        $conditions[] = new EqualityCondition(InternshipOrganizerPublication :: PROPERTY_PLACE_ID, $this->agreement->get_id());
        $conditions[] = new InCondition(InternshipOrganizerPublication :: PROPERTY_CONTENT_OBJECT_TYPE, $types);
        
        
        $query = $this->action_bar->get_query();
        
        if (isset($query) && $query != '')
        {
            
            $publication_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerPublication :: get_table_name());
            $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
            $object_alias = RepositoryDataManager :: get_instance()->get_alias(ContentObject :: get_table_name());
            
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*', $user_alias, true);
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*', $user_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerPublication :: PROPERTY_NAME, '*' . $query . '*', $publication_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerPublication :: PROPERTY_DESCRIPTION, '*' . $query . '*', $publication_alias, true);
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*', $object_alias, true);
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*', $object_alias, true);
            $conditions[] = new OrCondition($search_conditions);
        }
        
        return new AndCondition($conditions);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipOrganizerAgreements')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_AGREEMENT_ID);
    }

}
?>