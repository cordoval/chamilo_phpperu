<?php
/**
 * $Id: subscriber.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_manager.component
 */

class InternshipOrganizerAgreementManagerApproveLocationComponent extends InternshipOrganizerAgreementManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $agreement_id = Request :: get(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID);
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: APPROVE_LOCATION_RIGHT, $agreement_id, InternshipOrganizerRights :: TYPE_AGREEMENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $location_id = Request :: get(InternshipOrganizerAgreementManager :: PARAM_LOCATION_ID);
        $type = Request :: get(InternshipOrganizerAgreementManager :: PARAM_APPROVE_AGREEMENT_REL_LOCATION_TYPE);
        
        $datamanager = InternshipOrganizerDataManager :: get_instance();
        $agreement_rel_location = $datamanager->retrieve_agreement_rel_location($location_id, $agreement_id);
        if ($agreement_rel_location->set_location_type($type))
        {
            $message = htmlentities(Translation :: get('InternshipOrganizerAgreementRelLocationApproved'));
        }
        
        $this->redirect($message, false, array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id));
        exit();
    }
}
?>