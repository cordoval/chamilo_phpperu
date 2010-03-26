<?php
/**
 * $Id: reporting_chart_formatter.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.formatters
 * @author Michael Kyndt
 */
class ReportingChartFormatter extends ReportingFormatter
{
    private $instance;
	const DISPLAY_PIE = '3_1';
    const DISPLAY_BAR = '3_2';
	const DISPLAY_LINE = '3_3';
	const DISPLAY_FILLED_CUBIC = '3_4';
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

    function get_type_name($value)
    {
    	switch($value)
    	{
    		case self::DISPLAY_BAR : return 'bar'; break;
    		case self::DISPLAY_FILLED_CUBIC: return 'filledcubic'; break;
    		case self::DISPLAY_LINE : return 'line'; break;
    		case self::DISPLAY_PIE : return 'pie'; break;
    		default : return 'pie';
    	}
    }
    
	public function convert_reporting_data()
    {
    	$reporting_data = $this->get_block()->retrieve_data();
    	$chart = array();
    	$chart_description = array();
    	$chart_data = array();
    	
    	$chart_description['Position'] = 'Name';
    	$chart_description['Values'] = array();
    	foreach($reporting_data->get_rows() as $row_id => $row_name)
    	{
    		$chart_description['Values'][$row_id] = $row_name;
    	}
    	
 		$chart[1] = $chart_description;
 		 
    	foreach($reporting_data->get_categories() as $category_id => $category_name)
    	{
    		$category_array = array();
    		$category_array['Name'] = $category_name;
    		foreach ($reporting_data->get_rows() as $row_id => $row_name)
    		{
    			$category_array[$row_name] = $reporting_data->get_data_category_row($category_id, $row_id);
    		}
    		$chart_data[] = $category_array;
    	}
    	$chart[0] = $chart_data;
    	return $chart;
    }
    
    public function get_chart_instance()
    {
        //if (!isset ($this->instance)) {
        $chartformatter = 'Pchart';
        
        require_once dirname(__FILE__) . '/' . strtolower($chartformatter) . '/' . strtolower($chartformatter) . '_reporting_chart_formatter.class.php';
        $class = $chartformatter . 'ReportingChartFormatter';
        $this->instance = new $class($this->get_block());
        //}
        return $this->instance;
    } //get_instance
} //ReportingChartFormatter
?>
