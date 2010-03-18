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
        $all_data = $this->reporting_block->get_data();
        $data = $all_data[0];
        $datadescription = $all_data[1];
        $values = sizeof($datadescription["Values"]);
        $count = 1;
        
        $pager_params = array();
        $pager_params['mode'] = 'Sliding';
        $pager_params['perPage'] = 10;
        $pager_params['totalItems'] = count($datadescription["Values"]);
        $pager_params['urlVar'] = 'pageID_' . $this->reporting_block->get_id();
        
        $pager = $this->create_pager($pager_params);
        $pager_links = $this->get_pager_links($pager);
        $offset = $pager->getOffsetByPageId();
        
        $start = $offset[0];
        $end = $offset[1];
        
        if ($values > 1)
        {
            while ($count <= $values)
            {
                if ($count >= $start && $count <= $end)
                {
                    foreach ($data as $key => $value)
                    {
                        $html[] = $value["Name"] . ': ' . $value["Serie" . $count];
                        $html[] = '<br />';
                    }
                    //$count++;
                    $html[] = '<br />';
                }
                $count ++;
            }
        }
        else
        {
            foreach ($data as $key => $value)
            {
                $j = 0;
                foreach ($value as $key2)
                {
                    if (isset($datadescription["Description"]["Column" . $j]))
                    {
                        $html[] = $datadescription["Description"]["Column" . $j] . ': ' . $key2;
                        $html[] = '<br />';
                    }
                    else
                        $html[] = $key2 . " ";
                    $j ++;
                }
                $html[] = "<br />";
            }
        }
        $html[] = $pager_links;
        return implode("\n", $html);
    }

    public function ReportingTextFormatter(& $reporting_block)
    {
        $this->reporting_block = $reporting_block;
    }
} //ReportingTextFormatter
?>
