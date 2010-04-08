<?php
/**
 * $Id: bar_pchart_reporting_chart_formatter.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.formatters.pchart
 * @author Michael Kyndt
 */
class BarPchartReportingChartFormatter extends PchartReportingChartFormatter
{

    public function BarPchartReportingChartFormatter(&$reporting_block)
    {
        parent :: __construct($reporting_block);
    }

    protected function render_chart()
    {
        $all_data = $this->convert_reporting_data();

        $data = $all_data[0];
        $datadescription = $all_data[1];

        $width = 100 + count($data) * 50;
        if ($width < 500)
        {
            $width = 500;
        }
        $height = 270;

        $legend = 30 + sizeof($datadescription['Values']) * 30;
        $data = $this->strip_data_names($data);

        // Initialise the graph
        $pchart_graph = new pChart($width, $height + $legend);

        $pchart_graph->setFontProperties($this->font, 8);

        $pchart_graph->setGraphArea(40, 30, $width - 20, $height - $legend);

        $pchart_graph->drawFilledRoundedRectangle(7, 7, $width - 7, $height - 7 + $legend, 5, 240, 240, 240);
        //$Test->drawRoundedRectangle(5, 5, $width-5, $height-5, 5, 230, 230, 230);
        $pchart_graph->drawGraphArea(255, 255, 255, true);
        $pchart_graph->drawScale($data, $datadescription, SCALE_START0, 150, 150, 150, TRUE, 315, 2, true, 0, false);
        $pchart_graph->drawGrid(4, false, 230, 230, 230, 50);

        // Draw the 0 line
        $pchart_graph->setFontProperties($this->font, 6);
        $pchart_graph->drawTreshold(0, 143, 55, 72, TRUE, TRUE);

        // Draw the bar graph
        $pchart_graph->drawBarGraph($data, $datadescription, TRUE);

        // Finish the graph
        $pchart_graph->setFontProperties($this->font, 8);
        $pchart_graph->drawLegend(15, $height + 30, $datadescription, 255, 255, 255);
        $pchart_graph->setFontProperties($this->font, 10);
        $pchart_graph->drawTitle(50, 22, $this->get_block()->get_name_translation());

        return $pchart_graph;
    }

    /**
     * @see Reporting Formatter -> to_html
     */
    public function to_html()
    {
        return parent :: render_html($this->to_link('WEB'));
    } //to_html


    /**
     * @see Reporting Chart Formatter -> to_link
     */
    public function to_link($type = 'SYS')
    {
        return parent :: render_link($this->get_chart(), 'barchart', $type);
    }

}
?>