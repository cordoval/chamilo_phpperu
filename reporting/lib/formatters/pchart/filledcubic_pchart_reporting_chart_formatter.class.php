<?php
/**
 * $Id: filledcubic_pchart_reporting_chart_formatter.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.formatters.pchart
 * @author Michael Kyndt
 */
class FilledCubicPchartReportingChartFormatter extends PchartReportingChartFormatter
{

    public function FilledCubicPchartReportingChartFormatter(&$reporting_block)
    {
        parent :: __construct($reporting_block);
    }

    protected function render_chart()
    {
        $all_data = $this->convert_reporting_data();
        if (! $all_data)
        {
            return Path :: get(WEB_PATH) . 'layout/' . Theme :: get_theme() . '/images/common/unknown.jpg';
        }
        $image_id = md5('filledcubic' . serialize($all_data));
        $path = Path :: get(SYS_FILE_PATH) . 'temp/' . $image_id . '.png';
        if (! file_exists($path))
        {
            $data = $all_data[0];
            $datadescription = $all_data[1];
            
            //$width = $this->reporting_block->get_width()-20;
            $width = 100 + count($data) * 30;
            if ($width < 500)
            {
                $width = 500;
            }
            //$height = $this->reporting_block->get_height() - 50;
            $height = 270;
            $legend = 30 + sizeof($datadescription["Values"]) * 30;
            $data = $this->strip_data_names($data);
            
            // Initialise the graph
            $Test = new pChart($width, $height + $legend);
            $Test->setFontProperties($this->font, 8);
            $Test->setGraphArea(40, 30, $width - 20, $height - $legend);
            $Test->drawFilledRoundedRectangle(7, 7, $width - 7, $height - 7 + $legend, 5, 240, 240, 240);
            
            $Test->drawGraphArea(255, 255, 255, TRUE);
            $Test->drawScale($data, $datadescription, SCALE_START0, 150, 150, 150, TRUE, 315, 2, true, 0, false);
            $Test->drawGrid(4, TRUE, 230, 230, 230, 50);
            
            // Draw the 0 line
            $Test->setFontProperties($this->font, 6);
            $Test->drawTreshold(0, 143, 55, 72, TRUE, TRUE);
            
            // Draw the cubic curve graph
            $Test->drawFilledCubicCurve($data, $datadescription, .01, 50);
            
            // Finish the graph
            $Test->setFontProperties($this->font, 8);
            $Test->drawLegend(15, $height + 30, $datadescription, 255, 255, 255);
            $Test->setFontProperties($this->font, 10);
            $Test->drawTitle(50, 22, $this->get_block()->get_name_translation(), 50, 50, 50, $width * 0.6);
        	
            $Test->Render($path);
        }
        return Path :: get(WEB_FILE_PATH) . 'temp/' . $image_id . '.png';
    }

    /**
     * @see Reporting Formatter -> to_html
     */
    public function to_html()
    {
        return parent :: render_html($this->get_chart());
    } //to_html
}
?>