<?php

/**
 * $Id: reporting_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.reporting.component
 */

/**
 * @author Michael Kyndt
 */
class ReportingToolViewerComponent extends ReportingTool
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $rtv = ReportingViewer :: construct($this);

        $template_id = Request :: get(ReportingManager :: PARAM_TEMPLATE_ID);

        if (!isset($template_id))
        {
            $rtv->add_template_by_name('course_student_tracker_reporting_template', WeblcmsManager::APPLICATION_NAME);
        }
        else
        {
            $rtv->add_template_by_id($template_id);
        }

        $rtv->show_all_blocks();
        $rtv->run();
    }

    /* function display_header($trail)
      {
      $parameters = $this->get_parameters();

      $this->set_parameter(ReportingViewer :: PARAM_REPORTING_VIEWER_ACTION, null);
      $this->set_parameter(ReportingManager :: PARAM_TEMPLATE_ID, null);
      $this->set_parameter(WeblcmsManager :: PARAM_USERS, null);

      parent :: display_header();

      $this->set_parameters($parameters);
      } */

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('ReportingToolBrowserComponent')));
    }

}

?>