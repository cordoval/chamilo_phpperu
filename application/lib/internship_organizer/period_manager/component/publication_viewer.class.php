<?php

require_once dirname(__FILE__) . '/../period_manager.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/publisher/publication_table/publication_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/period_manager/component/viewer.class.php';


class InternshipOrganizerPeriodManagerPublicationViewerComponent extends InternshipOrganizerPeriodManager
{

    function run()
    {
        
        $publication_id = $_GET[self :: PARAM_PUBLICATION_ID];
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, $publication_id, InternshipOrganizerRights :: TYPE_PUBLICATION))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $publication = InternshipOrganizerDataManager :: get_instance()->retrieve_publication($publication_id);
        $place_id = $publication->get_place_id();
        
        switch ($place_id)
        {
            case InternshipOrganizerPublicationPlace :: AGREEMENT :
                $place_object = $this->retrieve_agreement($place_id);
                break;
            case InternshipOrganizerPublicationPlace :: MOMENT :
                $place_object = $this->retrieve_moment($place_id);
                break;
            case InternshipOrganizerPublicationPlace :: PERIOD :
                $place_object = $this->retrieve_period($place_id);
                break;
            default :
                //error: publication always needs a place_id and corrosponding place;
                break;
        }
        
        $content_object = $publication->get_content_object();
       
        $this->display_header();
        
        echo '<div>';
        $display = ContentObjectDisplay :: factory($content_object);
        echo $display->get_full_html();
        echo '</div>';
        
        $this->display_footer();
    }
    
function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS, self :: PARAM_PERIOD_ID => Request :: get(self :: PARAM_PERIOD_ID))), Translation :: get('BrowseInternshipOrganizerPeriods')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_PERIOD, self :: PARAM_PERIOD_ID => Request :: get(self :: PARAM_PERIOD_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerViewerComponent :: TAB_PUBLICATIONS)), Translation :: get('ViewInternshipOrganizerPeriod')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PERIOD_ID);
    }
    
}
?>