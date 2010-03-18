<?php
/**
 * $Id: reporting_html_formatter.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.formatters
 * @author Michael Kyndt
 */
class ReportingHtmlFormatter extends ReportingFormatter
{
    function ReportingHtmlFormatter($block)
    {
        //$this->reporting_block = $reporting_block;
        parent :: $block;
    }
    
    /**
     * @see Reporting Formatter -> to_html
     */
    public function to_html()
    {
        //return $this->reporting_block->get_data();
        return $this->get_block()->retrieve_data();
    }

}
?>
