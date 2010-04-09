<?php
/**
 * $Id: reporting_exporter.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib
 * @author Michael Kyndt
 */

abstract class ReportingExporter
{

    private $template;

    public function ReportingExporter($template)
    {
        $this->template = $template;
    }

    public static function factory($type, $template)
    {
        require_once dirname(__FILE__) . '/exporters/reporting_' . $type . '_exporter.class.php';
        $class = 'Reporting' . Utilities::underscores_to_camelcase($type) . 'Exporter';

        return new $class($template);
    } 
    
    function get_file_name()
    {
    	return $this->get_template()->get_name() . date('_Y-m-d_H-i-s');
    }
    
    abstract function export();
    
    function get_template()
    {
    	return $this->template;
    }
    
    function set_template($template)
    {
    	$this->template = $template;
    }
}
?>
