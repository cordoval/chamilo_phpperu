<?php
/**
 * $Id: task_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.calendar_event
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
/**
 * This class can be used to display calendar events
 */
class TaskDisplay extends ContentObjectDisplay
{

    // Inherited
    function get_full_html()
    {
        return parent :: get_full_html();
    }

    function get_description()
    {
        $description = parent :: get_description();
        $object = $this->get_content_object();             
        $date_format = Translation :: get('dateTimeFormatLong');
        
        $prepend = array();       
        $repeats = $object->repeats();
        
        if ($repeats)
        {
            $prepend[] = '<div class="task_range" style="font-weight: bold;">';
            $prepend[] = Translation :: get('Repeats');
            $prepend[] = ' ';
            $prepend[] = strtolower($object->get_repeat_as_string());
            $prepend[] = ' ';
            $prepend[] = Translation :: get('Until');
            $prepend[] = ' ';
            $prepend[] = DatetimeUtilities :: format_locale_date($date_format, $object->get_repeat_to());
            $prepend[] = '</div>';
        }
        else
        {
            $prepend[] = '<div class="task_range" style="font-weight: bold;">';
            $prepend[] = Translation :: get('From');
            $prepend[] = ' ';
            $prepend[] = DatetimeUtilities :: format_locale_date($date_format, $object->get_start_date());
            $prepend[] = ' ';
            $prepend[] = Translation :: get('Until');
            $prepend[] = ' ';
            $prepend[] = DatetimeUtilities :: format_locale_date($date_format, $object->get_end_date());
            $prepend[] = '</div>';
        }
        $html[] = '<div class="task_range" style="font-style: italic;">';
        $html[] = 'priority : ' . $object->get_task_priority_as_string() . '<br/>';
        $html[] = 'type of the task : ' . $object->get_task_type_as_string();
        $prepend[] = '</div>';
        
        return implode('', $prepend) . $description . implode("\n", $html);
    }
}
?>