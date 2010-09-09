<?php

require_once dirname(__FILE__) . '/../organisation_manager.class.php';
//require_once dirname ( __FILE__ ) . '/../organisation_manager_component.class.php';


//require_once dirname ( __FILE__ ) . '/rel_location_browser/rel_location_browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/location_browser/browser_table.class.php';

class InternshipOrganizerOrganisationManagerLocationViewerComponent extends InternshipOrganizerOrganisationManager
{
    
    //private $action_bar;
    private $location;
    private $region;
    private $organisation;

    function run()
    {
        
        $location_id = $_GET[InternshipOrganizerOrganisationManager :: PARAM_LOCATION_ID];
        $this->location = $this->retrieve_location($location_id);
        $location = $this->location;
        
        $region_id = $_GET[InternshipOrganizerOrganisationManager :: PARAM_REGION_ID];
        $this->region = $this->retrieve_internship_organizer_region($region_id);
        $region = $this->region;
        
        $organisation_id = $_GET[InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID];
        $this->organisation = $this->retrieve_organisation($organisation_id);
        $organisation = $this->organisation;
        
        $trail = BreadcrumbTrail :: get_instance();
        //$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
        //$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id)), $this->organisation->get_name()));
        //$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_LOCATION, InternshipOrganizerOrganisationManager :: PARAM_LOCATION_ID => $location_id, InternshipOrganizerOrganisationManager :: PARAM_REGION_ID => $region_id, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id)), $this->location->get_name()));
        
        //$this->action_bar = $this->get_action_bar ();
        

        $this->display_header($trail);
        
        //echo $this->action_bar->as_html ();
        //echo '<div id="action_bar_browser">';
        

        //echo '<div>';
        //echo $this->get_table ();
        //echo '</div>';
        //echo '</div>';
        echo '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_location.png);">';
        echo '<div class="title">' . Translation :: get('Details') . '</div>';
        echo '<b>' . Translation :: get('Name') . '</b>: ' . $location->get_name();
        echo '<br /><b>' . Translation :: get('Address') . '</b>: ' . $location->get_address();
        echo '<br /><b>' . Translation :: get('ZipCode') . '</b>: ' . $region->get_zip_code();
        echo '<br /><b>' . Translation :: get('City') . '</b>: ' . $region->get_city_name();
        echo '<br /><b>' . Translation :: get('Telephone') . '</b>: ' . $location->get_telephone();
        echo '<br /><b>' . Translation :: get('Fax') . '</b>: ' . $location->get_fax();
        echo '<br /><b>' . Translation :: get('Email') . '</b>: ' . $location->get_email();
        echo '<br /><b>' . Translation :: get('Description') . '</b>: ' . $location->get_description();
        echo '<div class="clear">&nbsp;</div>';
        echo '</div>';
        $this->display_footer();
    }

}
?>