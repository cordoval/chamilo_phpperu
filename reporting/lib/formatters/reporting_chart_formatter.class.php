<?php
/**
 * $Id: reporting_chart_formatter.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.formatters
 * @author Michael Kyndt
 */
class ReportingChartFormatter extends ReportingFormatter
{
    private $instance;
    private $reporting_block;

    /**
     * @see Reporting Formatter -> to_html
     */
    public function to_html()
    {
        return $this->get_chart_instance()->to_html();
    } //to_html

    
    /**
     * Returns the link to the image without html
     */
    public function to_link()
    {
        return $this->get_chart_instance()->to_link();
    }

    public function ReportingChartFormatter(&$reporting_block)
    {
        $this->reporting_block = $reporting_block;
    } //ReportingChartFormatter

    
    public function get_chart_instance()
    {
        //if (!isset ($this->instance)) {
        $chartformatter = 'Pchart';
        require_once dirname(__FILE__) . '/' . strtolower($chartformatter) . '/' . strtolower($chartformatter) . '_reporting_chart_formatter.class.php';
        $class = $chartformatter . 'ReportingChartFormatter';
        $this->instance = new $class($this->reporting_block);
        //}
        return $this->instance;
    } //get_instance
} //ReportingChartFormatter
?>
