<?php
/**
 * $Id: ical_export.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.export.ical
 */
require_once Path :: get_plugin_path() . 'icalcreator/iCalcreator.class.php';

/**
 * Exports learning object to the ical format (xml)
 * @see http://www.kigkonsult.se/iCalcreator/docs/using.html
 */
class IcalExport extends ContentObjectExport
{

    function IcalExport($content_object)
    {
        parent :: __construct($content_object);
    }

    public function export_content_object()
    {
        $content_object = $this->get_content_object();
        $dir = Path :: get(SYS_TEMP_PATH) . $content_object->get_owner_id() . '/';
        Filesystem :: create_dir($dir);
        $file = $dir . 'export_ical_' . $content_object->get_id() . '.ics';

        $ical = new vcalendar();
        $ical->setConfig('unique_id', Path :: get(WEB_PATH));
        $ical->setProperty('method', 'PUBLISH');
        $ical->setConfig('url', Path :: get(WEB_PATH));
        
        $vevent = new vevent();
        $vevent->setProperty('summary', mb_convert_encoding($content_object->get_title(), 'UTF-8'));
        
        $vevent->setProperty('dtstart', $this->get_date_in_ical_format($content_object->get_start_date()));
        $vevent->setProperty('dtend', $this->get_date_in_ical_format($content_object->get_end_date()));
        
        $vevent->setProperty('description', mb_convert_encoding($content_object->get_description(), 'UTF-8'));
        
        $owner = UserDataManager :: get_instance()->retrieve_user($content_object->get_owner_id());
        
        $vevent->setProperty('organizer', $owner->get_email());
        $vevent->setProperty('attendee', $owner->get_email());
        
        if ($content_object->repeats())
        {
            $vevent->setProperty('rrule', $this->get_rrule());
        }
        
        $ical->setComponent($vevent);
        $calendar = $ical->createCalendar();
        
        $handle = fopen($file, 'w+');
        fwrite($handle, $calendar);
        fclose($handle);
        
        return $file;
    }

    function get_rrule()
    {
        $rrule = array();
        
        $content_object = $this->get_content_object();
        $repeat = $content_object->get_repeat_type();
        
        switch ($repeat)
        {
            case CalendarEvent :: REPEAT_TYPE_DAY :
                $rrule['FREQ'] = 'DAILY';
                break;
            case CalendarEvent :: REPEAT_TYPE_WEEK :
                $rrule['FREQ'] = 'WEEKLY';
                break;
            case CalendarEvent :: REPEAT_TYPE_MONTH :
                $rrule['FREQ'] = 'MONTHLY';
                break;
            case CalendarEvent :: REPEAT_TYPE_YEAR :
                $rrule['FREQ'] = 'YEARLY';
                break;
            case CalendarEvent :: REPEAT_TYPE_BIWEEK :
                $rrule['FREQ'] = 'WEEKLY';
                $rrule['INTERVAL'] = '2';
                break;
            case CalendarEvent :: REPEAT_TYPE_WEEKDAYS :
                $rrule['FREQ'] = 'DAILY';
                $rrule['BYDAY'] = array(array('DAY' => 'MO'), array('DAY' => 'TU'), array('DAY' => 'WE'), array('DAY' => 'TH'), array('DAY' => 'FR'));
                break;
        }
        
        if (! $content_object->repeats_indefinately())
        {
            $rrule['UNTIL'] = $this->get_date_in_ical_format($content_object->get_repeat_to());
        }
        
        return $rrule;
    
    }

    function get_date_in_ical_format($date)
    {
        $y = date('Y', $date);
        $m = date('m', $date);
        $d = date('d', $date);
        $h = date('H', $date);
        $M = date('i', $date);
        $s = date('s', $date);
        
        return $y . $m . $d . 'T' . $h . $M . $s;
    }

}
?>