<?php

require_once dirname(__FILE__) . '/../mentor_manager.class.php';

class InternshipOrganizerMentorManagerViewerComponent extends InternshipOrganizerMentorManager
{
    
    private $action_bar;
    private $mentor;

    function run()
    {
        
        $mentor_id = $_GET[InternshipOrganizerMentorManager :: PARAM_MENTOR_ID];
        $this->mentor = $this->retrieve_mentor($mentor_id);
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerMentorManager :: PARAM_ACTION => InternshipOrganizerMentorManager :: ACTION_BROWSE_MENTOR)), Translation :: get('BrowseInternshipOrganizerMentors')));
        //$trail->add ( new Breadcrumb ( $this->get_url (array(InternshipOrganizerMentorManager::PARAM_ACTION => InternshipOrganizerMentorManager :: ACTION_VIEW_MENTOR, InternshipOrganizerMentorManager :: PARAM_MENTOR_ID => $mentor_id)), $this->mentor->get_name()) );
        

        $this->action_bar = $this->get_action_bar();
        
        $this->display_header($trail);
        
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        
        echo '<div>';
        echo $this->get_table();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

    function get_table()
    {
        $parameters = $this->get_parameters();
        $parameters[InternshipOrganizerMentorManager :: PARAM_MENTOR_ID] = $this->mentor->get_id();
        $table = new InternshipOrganizerMentorBrowserTable($this, $parameters, $this->get_condition());
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerMentorManager :: PARAM_MENTOR_ID => $this->mentor->get_id())));
        return $action_bar;
    }

    function get_condition()
    {
        //Klopt deze?
        $query = $this->action_bar->get_query();
        $conditions = array();
        $mentor_id = $this->mentor->get_id();
        $conditions[] = new EqualityCondition(InternshipOrganizerMentor :: PROPERTY_ID, $mentor_id);
        
        return new AndCondition($conditions);
    }
}
?>