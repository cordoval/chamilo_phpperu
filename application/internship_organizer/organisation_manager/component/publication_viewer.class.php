<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/publisher/publication_table/publication_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/viewer.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/location_viewer.class.php';

class InternshipOrganizerOrganisationManagerPublicationViewerComponent extends InternshipOrganizerOrganisationManager
{

    function run()
    {
        
        $publication_id = $_GET[self :: PARAM_PUBLICATION_ID];
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, $publication_id, InternshipOrganizerRights :: TYPE_PUBLICATION))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $publication = InternshipOrganizerDataManager :: get_instance()->retrieve_publication($publication_id);
        $place_id = $publication->get_place_id();
        
        switch ($place_id)
        {
            case InternshipOrganizerPublicationPlace :: LOCATION :
                $place_object = $this->retrieve_location($place_id);
                break;
            case InternshipOrganizerPublicationPlace :: MOMENT :
                $place_object = $this->retrieve_moment($place_id);
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
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerOrganisations')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => Request :: get(self :: PARAM_ORGANISATION_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerViewerComponent :: TAB_PUBLICATIONS)), Translation :: get('ViewInternshipOrganizerOrganisation')));
        
        $location_id = Request :: get(self :: PARAM_LOCATION_ID);
        if ($location_id)
        {
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_LOCATION, self :: PARAM_LOCATION_ID => $location_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerLocationViewerComponent :: TAB_PUBLICATIONS)), Translation :: get('ViewInternshipOrganizerLocation')));
        }
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_ORGANISATION_ID, self :: PARAM_LOCATION_ID);
    }

}
?>