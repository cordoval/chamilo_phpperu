<?php
/**
 * $Id: reporting_table_formatter.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.formatters
 * @author Michael Kyndt
 */
class ReportingTableFormatter extends ReportingFormatter
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
        
        if (Request :: get('table_' . $this->reporting_block->get_id() . '_column'))
        {
            $this->reporting_block->add_function_parameter('order_by', new ObjectTableOrder());
        }
        
        $orientation = $datadescription[Reporting :: PARAM_ORIENTATION];
        
        $j = 0;
        foreach ($data as $key => $value)
        {
            foreach ($value as $key2 => $value2)
            {
                $value[$j] = $value[$key2];
                unset($value[$key2]);
                $j ++;
            }
            $data[$key] = $value;
            $j = 0;
        }
        
        if ($orientation == Reporting :: ORIENTATION_HORIZONTAL)
        {
            foreach ($data as $key => $value)
            {
                $datadescription["Description"][$j] = $value[0];
                unset($value[0]);
                $data[$key] = $value;
                $j ++;
            }
            foreach ($data as $key => $value)
            {
                foreach ($value as $key2 => $value2)
                {
                    $data2[$key2 - 1][] = $value2;
                }
            }
            $data = $data2;
        }
        
        $column = (isset($datadescription['default_sort_column'])) ? $datadescription['default_sort_column'] : 0;
        
        if ($this->reporting_block->is_sortable())
            $table = new SortableTable('table_' . $this->reporting_block->get_id(), null, $this->reporting_block->get_function(), $column);
        else
            $table = new SortableTableFromArray($data, $column, 10, 'table_' . $this->reporting_block->get_id());
            //Todo: not a sortable table
        

        foreach ($_GET as $key => $value)
        {
            if (strstr($key, 'table_' . $this->reporting_block->get_id()))
                Request :: set_get($key, null);
        }
        $table->set_additional_parameters($_GET);
        
        $j = 0;
        foreach ($datadescription["Description"] as $key => $value)
        {
            if ($value != "")
            {
                $table->set_header($j, $value, true);
                $j ++;
            }
        }
        
        if (Request :: get('export'))
            return $table->toHTML_export();
        
        return $table->toHTML();
    }

    public function ReportingTableFormatter(&$reporting_block)
    {
        $this->reporting_block = $reporting_block;
    }
} //ReportingTextFormatter
?>
