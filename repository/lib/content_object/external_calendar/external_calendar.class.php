<?php
/**
 * $Id: external_calendar.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.link
 */
require PATH :: get_plugin_path() . 'icalcreator/iCalcreator.class.php';
require PATH :: get_plugin_path() . 'ical/ical_recurrence.class.php';

class ExternalCalendar extends ContentObject
{
    const PROPERTY_URL = 'url';
    const CACHE_TIME = 3600;
    

    
    const REPEAT_TYPE_NONE = 'NONE';
    const REPEAT_TYPE_DAY = 'DAILY';
    const REPEAT_TYPE_WEEK = 'WEEKLY';
    const REPEAT_TYPE_MONTH = 'MONTHLY';
    const REPEAT_TYPE_YEAR = 'YEARLY';
    const REPEAT_START = 'start';
    const REPEAT_END = 'end';
        
    private $calendar;

    function get_url()
    {
        return $this->get_additional_property(self :: PROPERTY_URL);
    }

    function set_url($url)
    {
        return $this->set_additional_property(self :: PROPERTY_URL, $url);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_URL);
    }
    
    function get_calendar()
    {
    	$ical_id = md5('ical_' . serialize($this->get_url()));
        $path = Path :: get(SYS_FILE_PATH) . 'temp/ical/' . $ical_id . '.ics';
        $timedif = @(time() - filemtime($path));
        
        //if (! file_exists($path) || $timedif > self :: CACHE_TIME)
        //{
            if ($f = @fopen($this->get_url(), 'r'))
            {
                $calendar_content = '';
                while (! feof($f))
                {
                    $calendar_content .= fgets($f, 4096);
                }
                fclose($f);
            }
            Filesystem :: write_to_file($path, $calendar_content);
        //}
		
        if (!isset($this->calendar))
        {
        	$calendar = new vcalendar();
        	$calendar->parse($path);
        	$calendar->sort();
        }
        
        return $calendar;
    }
    
    function get_events()
    {
    	$evnets = array();
    	foreach($this->get_calendar()->components as $component)
    	{
    		if (get_class($component) == 'vevent')
    		{
    			$events[] = $component;
    		}
    	}
    	return $events;
    }
    
    function count_events()
    {
    	$events = $this->get_events();
    	return count($events);
    }
    

    
    function get_repeats(vevent $event, $start_date, $end_date)
    {
    	$ical_recurrence = new IcalRecurrence($event, $start_date, $end_date);
    	$test = $ical_recurrence->get_repeats();
    	return $test;
    }
    
    
}
?>