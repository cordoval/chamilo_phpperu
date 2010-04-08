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

class ComplexDisplayReportingTemplateViewerComponent extends ComplexDisplayComponent
{
    private $params;
    private $template_name;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $rtv = new ReportingTemplateViewer($this);
        
        echo '<div id="trailbox2" style="padding:0px;">' . $this->get_parent()->get_breadcrumbtrail()->render() . '<br /><br /><br /></div>';
        $rtv->show_reporting_template_by_name($this->template_name, array('course_id' => Request :: get('course'), 'pid' => Request :: get('pid'), 'cid' => Request :: get('selected_cloi')));
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