<?php
require_once require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/viewer.class.php';

class InternshipOrganizerAgreementManagerMoverComponent extends InternshipOrganizerAgreementManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $agreement_id = Request :: get(self :: PARAM_AGREEMENT_ID);
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: ADD_LOCATION_RIGHT, $agreement_id, InternshipOrganizerRights :: TYPE_AGREEMENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $location_id = Request :: get(self :: PARAM_LOCATION_ID);
        $move_direction = Request :: get(self :: PARAM_MOVE_AGREEMENT_REL_LOCATION_DIRECTION);
        
        $datamanager = InternshipOrganizerDataManager :: get_instance();
        $agreement_rel_location = $datamanager->retrieve_agreement_rel_location($location_id, $agreement_id);
        if ($agreement_rel_location->move($move_direction))
        {
            
            if ($move_direction < 0)
            {
                $message = htmlentities(Translation :: get('InternshipOrganizerAgreementRelLocationPreferenceUp'));
            
            }
            else
            {
                $message = htmlentities(Translation :: get('InternshipOrganizerAgreementRelLocationPreferenceDown'));
            
            }
        
        }
        
        $this->redirect($message, false, array(self :: PARAM_ACTION => self :: ACTION_VIEW_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_LOCATIONS));
        exit();
    
    }
}
?>