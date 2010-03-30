<?php
/**
 * $Id: pie_pchart_reporting_chart_formatter.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.formatters.pchart
 * @author Michael Kyndt
 */
class PiePchartReportingChartFormatter extends PchartReportingChartFormatter
{

    public function PiePchartReportingChartFormatter(& $reporting_block)
    {
        parent :: __construct($reporting_block);
    }

    protected function render_chart()
    {
        $all_data = $this->convert_reporting_data();

	    $data = $all_data[0];
	    $datadescription = $all_data[1];
	    //$width = $this->reporting_block->get_width() - 20;
	    $width = 350;
	    $legend = sizeof($data) * 20;
	    //$height = $this->reporting_block->get_height() - 50;
	    $height = 270;
	    $data = $this->strip_data_names($data, 50);

	    // Initialise the graph
	    $pchart_graph = new pChart($width, $height + $legend);
	    $pchart_graph->loadColorPalette(Path :: get(SYS_LAYOUT_PATH) . Theme :: get_theme() . '/plugin/pchart/tones.txt');
	    $pchart_graph->drawFilledRoundedRectangle(7, 7, $width - 7, $height - 7 + $legend, 5, 240, 240, 240);

	    // This will draw a shadow under the pie chart
	    $pchart_graph->drawFilledCircle($width / 2, $height / 2, ($height - 2) * 0.4, 200, 200, 200);

	    // Draw the pie chart
	    $pchart_graph->setFontProperties($this->font, 8);
	    $pchart_graph->drawBasicPieGraph($data, $datadescription, $width / 2, $height / 2, $height * 0.4, PIE_PERCENTAGE, 250, 250, 250);
	    $pchart_graph->drawPieLegend(15, $height - 10, $data, $datadescription, 250, 250, 250);

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
        return parent :: render_link($this->get_chart(), 'piechart', $type);
    } //to_link
}
?>
