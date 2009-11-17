<?php
/**
 * $Id: reporting_html_formatter.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.formatters
 * @author Michael Kyndt
 */
class ReportingHtmlFormatter extends ReportingFormatter
{
    private $reporting_block;

    /**
     * @see Reporting Formatter -> to_html
     */
    public function to_html()
    {
        return $this->reporting_block->get_data();
    }

    public function ReportingHtmlFormatter(& $reporting_block)
    {
        $this->reporting_block = $reporting_block;
    }
} //ReportingHtmlFormatter
?>
