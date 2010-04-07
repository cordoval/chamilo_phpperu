<?php
/**
 * $Id: ical_import.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.import.ical
 */

/**
 * Exports learning object to the chamilo learning object format (xml)
 */
class IcalImport extends ContentObjectImport
{

    function IcalImport($content_object_file, $user, $category)
    {
        parent :: __construct($content_object_file, $user, $category);
    }

    public function import_content_object()
    {
    	
    	
        $file = $this->get_content_object_file();

        $content = file_get_contents($file['tmp_name']);
        $read_lines = explode("\n", $content);
        $count = count($read_lines);

        //unset empty line
        $counter = 0;
    	for($i = 0; $i < $count; $i ++)
        {
            $line = trim($read_lines[$i]);
            if ($line != '')
            {
                $lines[$counter] = $read_lines[$i];
                $counter++;
            }
        }
 
        for($i = 0; $i < $count; $i ++)
        {
            $line = rtrim($lines[$i]);
            
            
            if ($line == 'BEGIN:VEVENT')
            {
                $i = $this->import_event($lines, $i, $count);
            }
        }
        
        return $this->calendar_event_ids;
    }

    private $calendar_event_ids;

    public function import_event($lines, $i, $count)
    {
        $calendar_event = new CalendarEvent();
        $calendar_event->set_owner_id($this->get_user()->get_id());
        $calendar_event->set_parent_id($this->get_category());

        for($i; $i < $count; $i ++)
        {
            $line = trim($lines[$i]);

            while((substr($lines[$i+1], 0, 2) == '\t') || (substr($lines[$i+1], 0, 1) == ' '))
            {
            	$trimmed_line = trim($lines[$i+1]);
            	$i++;
            	$line .= $trimmed_line;

            }
            
            if ($line == 'END:VEVENT')
            {
                break;
            }

            if (substr($line, 0, 7) == 'SUMMARY')
            {
                $calendar_event->set_title(substr($line, 8));
            }

            if (substr($line, 0, 11) == 'DESCRIPTION')
            {
                $calendar_event->set_description(substr($line, 12));
            }

            if (substr($line, 0, 7) == 'DTSTART')
            {
                $start = substr($line, 8);
                
                $timezone = substr($start, 0, 4);
                if($timezone == 'TZID')
                {
                	$time_part = substr($start,strrpos($start,':')+1);
                	$time = strtotime($time_part);
                	$calendar_event->set_start_date($time);
            	}
            	else
            	{
                	$time = strtotime($start);
                	$calendar_event->set_start_date($time);
            	}
               
            }

            if (substr($line, 0, 5) == 'DTEND')
            {
            	$end = substr($line, 6);
                $timezone = substr($end, 0, 4);
                if($timezone == 'TZID')
                {
                	$time_part = substr($end,strrpos($end,':')+1);
                	$time = strtotime($time_part);
                	$calendar_event->set_end_date($time);
            	}
            	else
            	{
	               	$time = strtotime($end);
                	$calendar_event->set_end_date($time);
            	}
            }

            if (substr($line, 0, 5) == 'RRULE')
            {
                $rule = substr($line, 6);
                $parameters_list = explode(";", $rule);
                $parameters = array();
                foreach ($parameters_list as $parameter)
                {
                    $parameter_array = explode("=", $parameter);
                    $parameters[$parameter_array[0]] = $parameter_array[1];
                }

                if (isset($parameters['INTERVAL']))
                {
                    if ($parameters['INTERVAL'] == 2 && $parameters['FREQ'] == 'WEEKLY')
                    {
                        $calendar_event->set_repeat_type(CalendarEvent :: REPEAT_TYPE_BIWEEK);
                    }
                }
                else
                {
                    if ($parameters['FREQ'] == 'DAILY')
                    {
                        if ($parameters['BYDAY'] == 'MO,TU,WE,TH,FR')
                        {
                            $calendar_event->set_repeat_type(CalendarEvent :: REPEAT_TYPE_WEEKDAYS);
                        }
                        else
                        {
                            $calendar_event->set_repeat_type(CalendarEvent :: REPEAT_TYPE_DAY);
                        }
                    }

                    if ($parameters['FREQ'] == 'WEEKLY')
                    {
                        $calendar_event->set_repeat_type(CalendarEvent :: REPEAT_TYPE_WEEK);
                    }

                    if ($parameters['FREQ'] == 'MONTHLY')
                    {
                        $calendar_event->set_repeat_type(CalendarEvent :: REPEAT_TYPE_MONTH);
                    }

                    if ($parameters['FREQ'] == 'YEARLY')
                    {
                        $calendar_event->set_repeat_type(CalendarEvent :: REPEAT_TYPE_YEAR);
                    }
                }

                if ($calendar_event->repeats())
                {
                    if (isset($parameters['UNTIL']))
                    {
                        $calendar_event->set_repeat_to(strtotime($parameters['UNTIL']));
                    }
                }

            }

        }
        
        $calendar_event->create();
        $this->calendar_event_ids[] = $calendar_event->get_id();

        return $i;
    }

}
?>