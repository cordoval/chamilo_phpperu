<?php
/**
 * $Id: reporting.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * Receives a request, makes the reporting block retrieve its data & displays the block in the given format;
 * @package reporting.lib
 * @author Michael Kyndt
 */


class Reporting
{
    const PARAM_ORIENTATION = 'orientation';
    
    const ORIENTATION_VERTICAL = 'vertical';
    const ORIENTATION_HORIZONTAL = 'horizontal';

    /**
     * Generates a reporting block
     * @param ReportingBlock $reporting_block
     * @return html
     */
    public static function generate_block(&$reporting_block, $params)
    {
        if ($params[ReportingTemplate :: PARAM_DIMENSIONS] == ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS)
        {
            //$html[] = '<div id="'.$reporting_block->get_id().'" class="reporting_block" style="max-height:'.$reporting_block->get_height().';">';
            $html[] = '<div id="' . $reporting_block->get_id() . '" class="reporting_block">';
            $width = "<script>document.write(screen.width);</script>";
            //$reporting_block->set_width($width.'px');
        }
        else
        {
            $html[] = '<div id="' . $reporting_block->get_id() . '" class="reporting_block">';
            //$html[] = '<div id="'.$reporting_block->get_id().'" class="reporting_block" style="max-height:'.$reporting_block->get_height().';'.
        //'width:'.$reporting_block->get_width().';">';
        //$html[] = '<div id="'.$reporting_block->get_id().'" class="reporting_block" style="width:'.$reporting_block->get_width().';">';
        }
        $html[] = '<div class="reporting_header">';
        $html[] = '<div class="reporting_header_title">' . Translation :: get($reporting_block->get_name()) . '</div>';
        $html[] = '<div class="reporting_header_displaymode">';
        $html[] = '<select name="charttype" class="charttype">';
        foreach ($reporting_block->get_displaymodes() as $key => $value)
        {
            if ($key == $reporting_block->get_displaymode())
            {
                $html[] = '<option SELECTED value="' . $key . '">' . $value . '</option>';
            }
            else
            {
                $html[] = '<option value="' . $key . '">' . $value . '</option>';
            }
        }
        $html[] = '</select></div><div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        
        $html[] = '<div class="reporting_content">';
        $html[] = ReportingFormatter :: factory($reporting_block)->to_html();
        $html[] = '</div>';
        
        $html[] = '<div class="reporting_footer">';
        $html[] = '<div class="reporting_footer_export">';
        $html[] = $reporting_block->get_export_links();
        $html[] = '</div>&nbsp;<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        
        $html[] = '</div>';
        
        return implode("\n", $html);
    } //generate_block

    
    public static function generate_block_export(&$reporting_block, $params)
    {
        if ($params[ReportingTemplate :: PARAM_DIMENSIONS] == ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS)
        {
            $html[] = '<div id="' . $reporting_block->get_id() . '" class="reporting_block">';
        }
        else
        {
            $html[] = '<div id="' . $reporting_block->get_id() . '" class="reporting_block">';
        }
        $html[] = '<div class="reporting_header">';
        $html[] = '<div class="reporting_header_title"><b>' . Translation :: get($reporting_block->get_name()) . '</b></div>';
        $html[] = '</div>';
        
        $html[] = '<div class="reporting_content">';
        //remove links
        $data = ReportingFormatter :: factory($reporting_block)->to_html();
        $data = str_replace('</a>', '', $data);
        $data = preg_replace('/<a[^>]+href[^>]+>/', '', $data);
        $html[] = $data;
        $html[] = '</div>';
        
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        
        $html[] = '</div>';
        
        return implode("\n", $html);
    } //generate_block_export

    
    /**
     * Generates an array from a tracker
     * Currently only supports 1 serie
     * @todo support multiple series
     * @param Tracker $tracker
     * @return array
     */
    public static function array_from_tracker($tracker, $condition = null, $description = null)
    {
        $c = 0;
        $array = array();
        $trackerdata = $tracker->retrieve_tracker_items($condition);
        
        foreach ($trackerdata as $key => $value)
        {
            $arr[$value->get_name()][] = $value->get_value();
        }
        return self :: getSerieArray($arr, $description);
    } //array_from_tracker

    
    public static function getSerieArray($arr, $description = null)
    {
        $len = 50;
        $array = array();
        $i = 0;
        if (! isset($arr) || count($arr) == 0)
        {
            $arr[''][] = '<div style="text-align: center;">' . Translation :: get('NoSearchResults') . '</div>';
            unset($description);
            $description[self :: PARAM_ORIENTATION] = self :: ORIENTATION_HORIZONTAL;
        }
        foreach ($arr as $key => $value)
        {
            $serie = 1;
            $data[$i]["Name"] = $key;
            foreach ($value as $key2 => $value2)
            {
                $data[$i]["Serie" . $serie] = $value2;
                $serie ++;
            }
            $i ++;
        }
        
        $datadescription["Position"] = "Name";
        $count = count($data[0]) - 1;
        for($i = 1; $i <= $count; $i ++)
        {
            $datadescription["Values"][] = "Serie" . $i;
            if ($description && $count > 1 && count($description) < $count)
                $datadescription["Description"]["Serie" . $i] = $description[$i];
            else 
                if ($description)
                {
                    for($i = 0; $i < count($description); $i ++)
                    {
                        if ($description[$i] != "")
                            $datadescription["Description"]["Column" . $i] = $description[$i];
                    }
                }
        }
        if (isset($description[self :: PARAM_ORIENTATION]))
            $datadescription[self :: PARAM_ORIENTATION] = $description[self :: PARAM_ORIENTATION];
        else
            $datadescription[self :: PARAM_ORIENTATION] = ($serie - 1 == 1) ? self :: ORIENTATION_VERTICAL : self :: ORIENTATION_HORIZONTAL;
        
        if (isset($description['default_sort_column']))
            $datadescription['default_sort_column'] = $description['default_sort_column'];
        
        array_push($array, $data);
        array_push($array, $datadescription);
        return $array;
    } //getSerieArray

    
    public static function sort_array(&$arr, $tesor)
    {
        arsort($arr[$tesor]);
        $i = 0;
        foreach ($arr[$tesor] as $key => $value)
        {
            if ($i < sizeof($arr[$tesor]) / 2)
            {
                foreach ($arr as $key2 => $value2)
                {
                    if ($key2 != $tesor)
                    {
                        $bla = $arr[$key2][$key];
                        $arr[$key2][$key] = $arr[$key2][$i];
                        $arr[$key2][$i] = $bla;
                    }
                }
                $i ++;
            }
        }
    } //sort_array

    
    public static function get_params()
    {
        $params_session = $_SESSION[ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS];
        $params_get = Request :: get(ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS);
        
        foreach ($params_session as $key => $value)
        {
            $params[$key] = $value;
        }
        
        foreach ($params_get as $key => $value)
        {
            $params[$key] = $value;
        }
        
        if (! isset($params[ReportingManager :: PARAM_COURSE_ID]))
            $params[ReportingManager :: PARAM_COURSE_ID] = Request :: get('course');
        
        if (Request :: get('pid'))
            $params['pid'] = Request :: get('pid');
        if (Request :: get('cid') != null)
            $params['cid'] = Request :: get('cid');
            
        //$params['url'] = $parent->get_url();
        

        //        $params['parent'] = $parent;
        

        $_SESSION[ReportingManager :: PARAM_REPORTING_PARENT] = $parent;
        
        $_SESSION[ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS] = $params;
        
        return $params;
    }

    public static function get_weblcms_reporting_url($classname, $params)
    {
        require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';
        $manager = new WeblcmsManager();
        
        $url = $manager->get_reporting_url($classname, $params);
        
        $url = strstr($url, '?');
        return 'run.php' . $url;
    }
} //class reporting
?>