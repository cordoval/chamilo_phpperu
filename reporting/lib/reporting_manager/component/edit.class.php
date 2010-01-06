<?php
/**
 * $Id: edit.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.reporting_manager.component
 * @author Michael Kyndt
 */

class ReportingManagerEditComponent extends ReportingManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => ReportingManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Reporting')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES)), Translation :: get('Reporting')));
        
        $id = Request :: get(ReportingManager :: PARAM_TEMPLATE_ID);
        
        if ($id)
        {
            $reporting_template_registration = $this->retrieve_reporting_template_registration($id);
            
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ReportingManager :: ACTION_VIEW_TEMPLATE, ReportingManager :: PARAM_TEMPLATE_ID => $id)), Translation :: get($reporting_template_registration->get_title())));
            $trail->add_help('reporting general');
            
            if (! $this->get_user()->is_platform_admin())
            {
                $this->display_header($trail);
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
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Edit')));
                $this->display_header($trail);
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoReportingTemplateRegistrationSelected')));
        }
    }
}
?>
