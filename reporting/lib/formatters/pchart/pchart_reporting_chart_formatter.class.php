<?php
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
    protected $reporting_block;
    protected $font;

    /**
     * @see Reporting Formatter -> to_html
     */
    public function to_html()
    {
        return $this->get_pchart_instance()->to_html();
    } //to_html

    
    protected function strip_data_names($data)
    {
        foreach ($data as $key => $value)
        {
            foreach ($value as $key2 => $value2)
            {
                if ($key2 == "Name")
                {
                    $value[$key2] = Utilities :: truncate_string($value2, 30, false, '...');
                }
            }
            $data[$key] = $value;
        }
        return $data;
    }

    /**
     * @see Reporting Chart Formatter -> to_link
     */
    public function to_link($type = 'SYS')
    {
        return $this->get_pchart_instance()->to_link();
    }

    public function PchartReportingChartFormatter(&$reporting_block)
    {
        $this->reporting_block = $reporting_block;
        $this->font = Path :: get_plugin_path() . '/pChart/Fonts/tahoma.ttf';
    } //ReportingChartFormatter

    
    public function get_pchart_instance()
    {
        if (! isset(self :: $instance))
        {
            $pos = strpos($this->reporting_block->get_displaymode(), ':');
            $charttype = substr($this->reporting_block->get_displaymode(), $pos + 1);
            require_once dirname(__FILE__) . '/' . strtolower($charttype) . '_pchart_reporting_chart_formatter.class.php';
            $class = $charttype . 'PchartReportingChartFormatter';
            $this->instance = new $class($this->reporting_block); // (self :: $charttype);
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

    protected function render_link($chart, $chartname = 'chart', $type = 'SYS')
    {
        $random = rand();
        // Render the pie chart to a temporary file
        $path = Path :: get(SYS_FILE_PATH) . 'temp/' . $this->reporting_block->get_name() . '_' . $chartname . $random . '.png';
        $chart->Render($path);
        
        // Return the link to the file
        $path = Path :: get($type . _FILE_PATH) . 'temp/' . $this->reporting_block->get_name() . '_' . $chartname . $random . '.png';
        return $path;
    }
}
?>
