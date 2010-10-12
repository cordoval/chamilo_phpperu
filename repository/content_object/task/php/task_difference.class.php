<?php
namespace repository\content_object\task;
/**
 * $Id: task_difference.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.calendar_event
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
/**
 * This class can be used to get the difference between calendar events
 */
class TaskDifference extends ContentObjectDifference
{

    function get_difference()
    {
        $date_format = Translation :: get('dateTimeFormatLong');
        
        $object = $this->get_object();
        $version = $this->get_version();
        
        $object_string = htmlentities(Translation :: get('From') . ' ' . DatetimeUtilities :: format_locale_date($date_format, $object->get_due_date()). ' ' . Translation :: get('Until') . ' ' . DatetimeUtilities :: format_locale_date($date_format, $object->get_end_date()));
        $object_string = explode("\n", strip_tags($object_string));
        
        $version_string = htmlentities(Translation :: get('From') . ' ' . DatetimeUtilities :: format_locale_date($date_format, $version->get_due_date()) . ' ' . DatetimeUtilities :: format_locale_date($date_format, $version->get_end_date()));
        $version_string = explode("\n", strip_tags($version_string));
        
        $td = new Difference_Engine($object_string, $version_string);
        
        return array_merge($td->getDiff(), parent :: get_difference());
    }
}
?>