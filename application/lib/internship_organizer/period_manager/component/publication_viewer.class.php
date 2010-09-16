<?php

require_once dirname(__FILE__) . '/../period_manager.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/publisher/publication_table/publication_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/viewer.class.php';

class InternshipOrganizerPeriodManagerPublicationViewerComponent extends InternshipOrganizerPeriodManager
{

    function run()
    {
        
        $publication_id = $_GET[InternshipOrganizerAgreementManager :: PARAM_PUBLICATION_ID];
        
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
        
        $trail = BreadcrumbTrail :: get_instance();
        $this->display_header($trail);
        
        echo '<div>';
        $display = ContentObjectDisplay :: factory($content_object);
        echo $display->get_full_html();
        echo '</div>';
        
        $this->display_footer();
    }
}
?>