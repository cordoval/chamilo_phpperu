<?php

require_once dirname(__FILE__) . '/../organisation_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/location_browser/browser_table.class.php';

class InternshipOrganizerOrganisationManagerLocationViewerComponent extends InternshipOrganizerOrganisationManager
{
 
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
        
       
        $this->display_header();
        
       
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