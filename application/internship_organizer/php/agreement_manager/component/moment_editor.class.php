<?php

require_once Path :: get_application_path() . 'internship_organizer/php/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'internship_organizer/php/forms/moment_form.class.php';
require_once Path :: get_application_path() . 'internship_organizer/php/agreement_manager/component/viewer.class.php';


class InternshipOrganizerAgreementManagerMomentEditorComponent extends InternshipOrganizerAgreementManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $moment_id = Request :: get(self :: PARAM_MOMENT_ID);
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, $moment_id, InternshipOrganizerRights :: TYPE_MOMENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $moment = $this->retrieve_moment($moment_id);
        
        $form = new InternshipOrganizerMomentForm(InternshipOrganizerMomentForm :: TYPE_EDIT, $moment, $this->get_url(array(self :: PARAM_MOMENT_ID => $moment->get_id())), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->update_moment();
            $this->redirect($success ? Translation :: get('InternshipOrganizerMomentUpdated') : Translation :: get('InternshipOrganizerMomentNotUpdated'), ! $success, array(self :: PARAM_ACTION => self :: ACTION_VIEW_AGREEMENT, self :: PARAM_AGREEMENT_ID => $moment->get_agreement_id(), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_MOMENTS));
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
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_MOMENTS)), Translation :: get('ViewInternshipOrganizerAgreement')));
        
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_AGREEMENT_ID, self :: PARAM_MOMENT_ID);
    }

}
?>