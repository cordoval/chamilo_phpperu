<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/forms/agreement_subscribe_mentor_form.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/viewer.class.php';


class InternshipOrganizerAgreementManagerSubscribeMentorComponent extends InternshipOrganizerAgreementManager
{
    private $agreement;
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('agreement subscribe mentor');
        
        $agreement_id = Request :: get(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID);
        $this->agreement = $this->retrieve_agreement($agreement_id);
       
        $form = new InternshipOrganizerAgreementSubscribeMentorForm($this->agreement, $this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => Request :: get(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_agreement_rel_mentor();
            if ($success)
            {
                $this->redirect(Translation :: get('InternshipOrganizerAgreementRelMentorCreated'), (false), array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $this->agreement->get_id(), DynamicTabsRenderer::PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_MENTOR));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerAgreementRelMentorCreated'), (false), array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $this->agreement->get_id(), DynamicTabsRenderer::PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_MENTOR));
            }
        }
        else
        {
            $this->display_header($trail, true);
            $form->display();
            $this->display_footer();
        }
    }

    function get_agreement()
    {
        return $this->agreement;
    }

}
?>