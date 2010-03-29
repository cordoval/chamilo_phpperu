<?php
/**
 * $Id: reporting_table_formatter.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.formatters
 * @author Michael Kyndt
 */
class ReportingTableFormatter extends ReportingFormatter
{
    /**
     * @see Reporting Formatter -> to_html
     */
    public function to_html()
    {
    	$reporting_data = $this->get_block()->retrieve_data();
        /*$data = $reporting_data[0];
        $datadescription = $reporting_data[1];

        if (Request :: get('table_' . $this->get_block->get_id() . '_column'))
        {
            $this->get_block->add_function_parameter('order_by', new ObjectTableOrder());
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

        $table_headers = array();
        if ($orientation == Reporting :: ORIENTATION_HORIZONTAL)
        {
            foreach ($data as $key => $value)
            {
				$table_headers[] = $value[0];
                //$datadescription["Description"][$j] = $value[0];
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
    	else
        {
            $table_headers[] = '';
            foreach ($datadescription['Values'] as $serie)
            {

                if (array_key_exists($serie, $datadescription['Description']))
                {
                    $table_headers[] = $datadescription['Description'][$serie];
                }
                else
                {
                    $table_headers[] = $serie;
                }

            }
        }

        $column = (isset($datadescription['default_sort_column'])) ? $datadescription['default_sort_column'] : 0;
        */
        /*if ($this->get_block->is_sortable())
            $table = new SortableTable('table_' . $this->get_block->get_id(), 'count_data', 'retrieve_data', $column);
        else*/
            $table = new SortableTableFromArray($this->convert_reporting_data(), null, 20, 'table_' . $this->get_block()->get_name());
            //Todo: not a sortable table


        /*foreach ($_GET as $key => $value)
        {
            if (strstr($key, 'table_' . $this->get_block->get_id()))
                Request :: set_get($key, null);
        }
        $table->set_additional_parameters($_GET);

        $j = 0;
        //foreach ($datadescription["Description"] as $key => $value)
        foreach ($table_headers as $key => $value)
        {
            //if ($value != "")
            //{
                $table->set_header($j, $value, true);
                $j ++;
            //}
        }

        if (Request :: get('export'))
            return $table->toHTML_export();
            */
        $parameters = $this->get_block()->get_parent()->get_parameters();
        $parameters[ReportingManager::PARAM_REPORTING_BLOCK_ID] = $this->get_block()->get_id();
        $parameters[ReportingFormatterForm::FORMATTER_TYPE] = $this->get_block()->get_displaymode();
        $parameters = array_merge($this->get_block()->get_parent()->get_parent()->get_parameters(), $parameters);
        $table->set_additional_parameters($parameters);
        $j = 1;
        $table->set_header(0, '', false);
        foreach($reporting_data->get_rows() as $row)
        {
        	$table->set_header($j, $row);
        }
        return $table->toHTML();
    }

    public function convert_reporting_data()
    {
    	$reporting_data = $this->get_block()->retrieve_data();
    	$table_data = array();
    	foreach($reporting_data->get_categories() as $category_id => $category_name)
    	{
    		$category_array = array();
    		$category_array[] = $category_name;
    		foreach ($reporting_data->get_rows() as $row_id => $row_name)
    		{
    			$category_array[] = $reporting_data->get_data_category_row($category_id, $row_id);
    		}
    		$table_data[] = $category_array;
    	}
    	return $table_data;
    }

    function get_total_number_from_reporting_block()
    {
    	return $this->get_block->get_total_number();
    }
} //ReportingTextFormatter
?>
