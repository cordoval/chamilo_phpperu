<?php

require_once dirname(__FILE__) . '/../mentor_manager.class.php';
require_once dirname(__FILE__) . '/browser/browser_table.class.php';

class InternshipOrganizerMentorManagerBrowserComponent extends InternshipOrganizerMentorManager
{
    private $action_bar;

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerMentorManager :: PARAM_ACTION => InternshipOrganizerMentorManager :: ACTION_BROWSE_MENTOR)), Translation :: get('BrowseInternshipOrganizerMentors')));
        
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
        $table = new InternshipOrganizerMentorBrowserTable($this, $parameters, $this->get_condition());
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerMentor'), Theme :: get_common_image_path() . 'action_create.png', $this->get_create_mentor_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        $action_bar->set_search_url($this->get_url());
        
        return $action_bar;
    }

    function get_condition()
    {
        //Klopt deze?
        $query = $this->action_bar->get_query();
        $condition = null;
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMentor :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMentor :: PROPERTY_LASTNAME, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMentor :: PROPERTY_EMAIL, '*' . $query . '*');
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMentor :: PROPERTY_TELEPHONE, '*' . $query . '*');
            $condition = new OrCondition($search_conditions);
        }
        return $condition;
    }
}
?>