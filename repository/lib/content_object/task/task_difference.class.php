<?php
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
        
        $object_string = htmlentities(Translation :: get('From') . ' ' . DatetimeUtilities :: convert_time_to_timezone($object->get_due_date(), $date_format). ' ' . Translation :: get('Until') . ' ' . DatetimeUtilities :: convert_time_to_timezone($object->get_end_date(), $date_format));
        $object_string = explode("\n", strip_tags($object_string));
        
        $version_string = htmlentities(Translation :: get('From') . ' ' . DatetimeUtilities :: convert_time_to_timezone($version->get_due_date(), $date_format) . ' ' . DatetimeUtilities :: convert_time_to_timezone($version->get_end_date(), $date_format));
        $version_string = explode("\n", strip_tags($version_string));
        
        $td = new Difference_Engine($object_string, $version_string);
        
        return array_merge($td->getDiff(), parent :: get_difference());
    }
}
?>