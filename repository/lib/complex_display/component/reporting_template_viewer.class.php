<?php
/**
 * $Id: reporting_template_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */

/**
 * Description of reporting_template_viewerclass
 *
 * @author Soliber
 */

require_once PATH :: get_application_path() . '/lib/weblcms/weblcms_manager/weblcms_manager.class.php';

class ComplexDisplayComponentReportingTemplateViewerComponent extends ComplexDisplayComponent
{
    private $params;
    private $template_name;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
    	$rtv = new ReportingViewer($this);
        $rtv->add_template_by_name($this->template_name, $this->get_parent()->get_application_name());
        $rtv->set_breadcrumb_trail($this->get_parent()->get_breadcrumbtrail());
        $rtv->show_all_blocks();
        
        $rtv->run();
    }

    function get_template_name()
    {
        return $this->template_name;
    }

    function set_template_name($name)
    {
        $this->template_name = $name;
    }

    function get_params()
    {
        return $this->params;
    }

    function set_params($params)
    {
        $this->params = $params;
    }
}
?>