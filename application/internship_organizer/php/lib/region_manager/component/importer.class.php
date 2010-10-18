<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'forms/region_import_form.class.php';

class InternshipOrganizerRegionManagerImporterComponent extends InternshipOrganizerRegionManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $region_id = Request::get(self :: PARAM_REGION_ID);
    	
    	$import_form = new InternshipOrganizerRegionImportForm('import', 'post', $this->get_url(), $this->get_parameter(self :: PARAM_REGION_ID), $this->get_user());
        
        if ($import_form->validate())
        {
            $success = $import_form->import_region();
            
            $messages = array();
            $errors = array();
            if ($success)
            {
                $messages[] = Translation :: translate('RegionImported');
            }
            else
            {
                $errors[] = Translation :: translate('RegionNotImported');
            }
            
            $messages = array_merge($messages, $import_form->get_messages());
            $warnings = $import_form->get_warnings();
            $errors = array_merge($errors, $import_form->get_errors());
            $parameters = array(self :: PARAM_ACTION => self :: ACTION_BROWSE_REGIONS, self :: PARAM_REGION_ID => $region_id);
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
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_REGIONS)), Translation :: get('BrowseInternshipOrganizerRegions')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_REGION_ID);
    }
}
?>