<?php
namespace reporting;

use common\libraries\Utilities;
use common\libraries\Path;

/**
 * $Id: pchart_reporting_chart_formatter.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.formatters.pchart
 * @author Michael Kyndt
 */
require_once Path :: get_plugin_path() . '/pChart/pChart/pChart.class';
require_once Path :: get_plugin_path() . '/pChart/pChart/pData.class';

class PchartReportingChartFormatter extends ReportingChartFormatter
{
    private $instance;
    protected $font;

    /**
     * @see Reporting Formatter -> to_html
     */
    public function to_html()
    {
        return $this->get_pchart_instance()->to_html();
    } //to_html


    public function get_chart()
    {
        $all_data = $this->get_block()->retrieve_data();
        if ($all_data == null)
        {
            return null;
        }
        else
        {
            return $this->render_chart();
        }
    }

    protected function strip_data_names($data)
    {
        foreach ($data as $key => $value)
        {
            foreach ($value as $key2 => $value2)
            {
                if ($key2 == "Name")
                {
                    $value[$key2] = Utilities :: truncate_string(trim(html_entity_decode(strip_tags($value2), ENT_COMPAT, 'utf-8')), 30, false, '...');
                }
            }
            $data[$key] = $value;
        }
        return $data;
    }

    public function __construct(&$reporting_block)
    {
        parent :: __construct($reporting_block);
        $this->font = Path :: get_plugin_path() . 'pChart/Fonts/tahoma.ttf';
    } //ReportingChartFormatter


    public function get_pchart_instance()
    {
        if (! isset(self :: $instance))
        {
            $display_mode = $this->get_block()->get_displaymode();
            $display_mode = explode('_', $display_mode);
            $type = self :: get_type_name($display_mode[0] . '_' . $display_mode[1]);

            require_once dirname(__FILE__) . '/' . strtolower($type) . '_pchart_reporting_chart_formatter.class.php';
            $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'PchartReportingChartFormatter';
            $this->instance = new $class($this->get_block()); // (self :: $charttype);
        }
        return $this->instance;
    } //get_instance


    /**
     * Generates an image of the chart in a temporary folder and returns
     * html referring to this image.
     * @param Chart $chart
     * @param String $chartname
     * @return html
     */
    protected function render_html($path)
    {
        //$random = rand();
        // Render the pie chart to a temporary file
        //$path = Path :: get(SYS_FILE_PATH) . 'temp/'.$this->reporting_block->get_name().'_'.$chartname . $random . '.png';
        //$chart->Render($path);


        // Return the html code to the file
        //$path = Path :: get(WEB_FILE_PATH) . 'temp/'.$this->reporting_block->get_name().'_'.$chartname . $random . '.png';
        return '<img src="' . $path . '" border="0" />';
    }

    public function convert_reporting_data()
    {
        $reporting_data = $this->get_block()->retrieve_data();
        if ($reporting_data->is_empty())
        {
            return false;
        }
        else
        {
            $chart = array();
            $chart_description = array();
            $chart_data = array();

            $chart_description['Position'] = 'Name';
            $chart_description['Values'] = array();
              $chart_description['Description'] = array();
            foreach ($reporting_data->get_rows() as $row_id => $row_name)
            {
                $chart_description['Values'][$row_id] = 'Serie'.$row_id;
                $chart_description['Description']['Serie'.$row_id] = trim(trim(trim(html_entity_decode(strip_tags($row_name), ENT_COMPAT, 'utf-8')), "\xC2\xA0" ));
            }

            $chart[1] = $chart_description;

            foreach ($reporting_data->get_categories() as $category_id => $category_name)
            {
                $category_array = array();
                $category_array['Name'] = trim(trim(html_entity_decode(strip_tags($category_name), ENT_COMPAT, 'utf-8')), "\xC2\xA0");
                foreach ($reporting_data->get_rows() as $row_id => $row_name)
                {
                    $category_array['Serie'.$row_id] = $reporting_data->get_data_category_row($category_id, $row_id);
                }
                $chart_data[] = $category_array;
            }

            $chart[0] = $chart_data;
            return $chart;
        }
    }
}
?>