<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'forms/organisation_import_form.class.php';

class InternshipOrganizerOrganisationManagerImporterComponent extends InternshipOrganizerOrganisationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
    if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_IMPORT, InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
    	
    	
    	$import_form = new InternshipOrganizerOrganisationImportForm('import', 'post', $this->get_url(), $this->get_user());
        
        if ($import_form->validate())
        {
            $success = $import_form->import_organisation();
            
            $messages = array();
            $errors = array();
            if ($success)
            {
                $messages[] = Translation :: translate('OrganisationImported');
            }
            else
            {
                $errors[] = Translation :: translate('OrganisationNotImported');
            }
            
            $messages = array_merge($messages, $import_form->get_messages());
            $warnings = $import_form->get_warnings();
            $errors = array_merge($errors, $import_form->get_errors());
            $parameters = array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION);
            $parameters[self :: PARAM_MESSAGE] = implode('<br/>', $messages);
            $parameters[self :: PARAM_WARNING_MESSAGE] = implode('<br/>', $warnings);
            $parameters[self :: PARAM_ERROR_MESSAGE] = implode('<br/>', $errors);
            
            $this->simple_redirect($parameters);
        
        }
        else
        {
            $this->display_header();
            $import_form->display();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerOrganisations')));
    }
    
}
?>