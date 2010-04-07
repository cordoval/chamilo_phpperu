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
            $arr[$value->get_name()] = $value->get_value();
        }
        return $arr;
        //return self :: get_serie_array($arr, $description);
    } //array_from_tracker

    
    public static function get_serie_array($arr, $description = null)
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

    public static function get_weblcms_reporting_url($classname, $params)
    {
        require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';
        $manager = new WeblcmsManager();
        
        $url = $manager->get_reporting_url($classname, $params);
        
        $url = strstr($url, '?');
        return 'run.php' . $url;
    }
    
    public static function get_name_registration($name, $application)
    {
    	$conditions = array();
    	$conditions[] = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_TEMPLATE, $name);
    	$conditions[] = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_APPLICATION, $application);
    	$condition = new AndCondition($conditions);
    	
    	$registrations = ReportingDataManager::get_instance()->retrieve_reporting_template_registrations($condition);
    	if ($registrations->size() == 1)
    	{
    		return $registrations->next_result();
    	}
    	else
    	{
    		return null;
    	}
    }
} //class reporting
?>