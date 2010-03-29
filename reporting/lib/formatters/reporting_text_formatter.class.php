<?php
/**
 * $Id: reporting_text_formatter.class.php 215 2009-11-13 14:07:59Z vanpouckesven $ 
 * @package reporting.lib.formatters
 * @author Michael Kyndt
 */
class ReportingTextFormatter extends ReportingFormatter
{
    private $reporting_block;

    /**
     * @see Reporting Formatter -> to_html
     */
    public function to_html()
    {
    	$reporting_data = $this->get_block()->retrieve_data();
        $data = $reporting_data->get_data();
        $values = sizeof($reporting_data->get_rows());
        $count = 1;
        
        /*$pager_params = array();
        $pager_params['mode'] = 'Sliding';
        $pager_params['perPage'] = 10;
        $pager_params['totalItems'] = $values;
        $pager_params['urlVar'] = 'pageID_' . $this->get_block()->get_id();
        
        $pager = $this->create_pager($pager_params);
        $pager_links = $this->get_pager_links($pager);
        $offset = $pager->getOffsetByPageId();*/
        
        $start = $offset[0];
        $end = $offset[1];
        
        if ($values > 1)
        {
            
        	foreach ($reporting_data->get_rows() as $row_id => $row_name)
        	{
	        	$html[] = $row_name . '<br />';
        		$categories = $reporting_data->get_categories();
	            foreach($categories as $category_id => $category_name)
	            {
	                  $html[] = $category_name . ': ' . $reporting_data->get_data_category_row($category_id, $row_id) .'<br />';  	
	            }
	            $html[] = '<br />';
        	}
        }
        else
        {
        	$categories = $reporting_data->get_categories();
        	$rows = $reporting_data->get_rows();
        	$rows_id = array_keys($rows);
            foreach($categories as $category_id => $category_name)
            {
            	  $html[] = $category_name . ': ' . $reporting_data->get_data_category_row($category_id, $rows_id[0]) .'<br />';      	
            }
        }
        //$html[] = $pager_links;
        return implode("\n", $html);
    }

} //ReportingTextFormatter
?>
