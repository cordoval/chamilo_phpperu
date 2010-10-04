<?php

require_once dirname(__FILE__) . '/../agreement_manager.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/publisher/publication_table/publication_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/viewer.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/moment_viewer.class.php';

class InternshipOrganizerAgreementManagerPublicationViewerComponent extends InternshipOrganizerAgreementManager
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
            case InternshipOrganizerPublicationPlace :: AGREEMENT :
                $place_object = $this->retrieve_agreement($place_id);
                break;
            case InternshipOrganizerPublicationPlace :: MOMENT :
                $place_object = $this->retrieve_moment($place_id);
                break;
            default :
                //error: publication always needs a place_id and corrosponding place;
                break;
        }
        
        $content_object = $publication->get_content_object();
        
        $type = $content_object->get_type();
        if ($type == Survey :: get_type_name())
        {
            ComplexDisplay :: launch($type, $this, false);
        }
        else
        {
            $display = ContentObjectDisplay :: factory($content_object);
            
            $this->display_header();
            echo '<div>';
            echo $display->get_full_html();
            echo '</div>';
            $this->display_footer();
        
        }
    
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipOrganizerAgreements')));
        $moment_id = Request :: get(self :: PARAM_MOMENT_ID);
        if ($moment_id)
        {
            $moment = $this->retrieve_moment($moment_id);
            $agreement_id = $moment->get_agreement_id();
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_MOMENTS)), Translation :: get('ViewInternshipOrganizerAgreement')));
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_MOMENT, self :: PARAM_MOMENT_ID => $moment_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerMomentViewerComponent :: TAB_PUBLICATIONS)), Translation :: get('ViewInternshipOrganizerMoment')));
        }
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_AGREEMENT_ID, self :: PARAM_MOMENT_ID);
    }

}
?>