<?php

require_once Path :: get_application_path() . 'internship_organizer/php/forms/agreement_subscribe_mentor_form.class.php';
require_once Path :: get_application_path() . 'internship_organizer/php/agreement_manager/component/viewer.class.php';

class InternshipOrganizerAgreementManagerSubscribeMentorComponent extends InternshipOrganizerAgreementManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $agreement_id = Request :: get(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID);
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: ADD_MENTOR_RIGHT, $agreement_id, InternshipOrganizerRights :: TYPE_AGREEMENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $agreement = $this->retrieve_agreement($agreement_id);
        
        $form = new InternshipOrganizerAgreementSubscribeMentorForm($agreement, $this->get_url(array(self :: PARAM_AGREEMENT_ID => Request :: get(self :: PARAM_AGREEMENT_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_agreement_rel_mentor();
            if ($success)
            {
                $this->redirect(Translation :: get('InternshipOrganizerAgreementRelMentorCreated'), (false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_MENTOR));
            }
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipOrganizerAgreements')));
        $agreement_id = Request :: get(self :: PARAM_AGREEMENT_ID);
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_MENTOR)), Translation :: get('ViewInternshipOrganizerAgreement')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_AGREEMENT_ID);
    }

}
?>