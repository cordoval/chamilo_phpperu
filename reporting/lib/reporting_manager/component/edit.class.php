<?php
/**
 * $Id: edit.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.reporting_manager.component
 * @author Michael Kyndt
 */

class ReportingManagerEditComponent extends ReportingManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(ReportingManager :: PARAM_TEMPLATE_ID);
        
        if ($id)
        {
            $reporting_template_registration = $this->retrieve_reporting_template_registration($id);
            
            if (! $this->get_user()->is_platform_admin())
            {
                $this->display_header();
                Display :: error_message(Translation :: get("NotAllowed"));
                $this->display_footer();
                exit();
            }
            
            $form = new ReportingTemplateRegistrationForm(ReportingTemplateRegistrationForm :: TYPE_EDIT, $reporting_template_registration, $this->get_url(array(ReportingManager :: PARAM_TEMPLATE_ID => $id)));
            
            if ($form->validate())
            {
                $success = $form->update_reporting_template_registration();
                $this->redirect(Translation :: get($success ? 'ReportingTemplateRegistrationUpdated' : 'ReportingTemplateRegistrationNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES));
            }
            else
            {
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoReportingTemplateRegistrationSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(ReportingManager :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES)), Translation :: get('ReportingManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('reporting_edit');
    }
    
    function get_additional_parameters()
    {
    	return array(ReportingManager :: PARAM_TEMPLATE_ID);
    }
}
?>