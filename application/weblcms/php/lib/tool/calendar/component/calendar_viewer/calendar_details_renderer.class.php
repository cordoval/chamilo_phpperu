<?php
/**
 * $Id: calendar_details_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.calendar.component.calendar_viewer
 */
require_once dirname(__FILE__) . '/../../../../browser/list_renderer/content_object_publication_details_renderer.class.php';
class CalendarDetailsRenderer extends ContentObjectPublicationDetailsRenderer
{

    function CalendarDetailsRenderer($browser)
    {
        parent :: __construct($browser);
    }

    function render_description($publication)
    {
        $event = $publication->get_content_object();
        $html[] = '<em>';
        //TODO: date formatting
        $html[] = htmlentities(Translation :: get('From')) . ': ' . date('r', $event->get_start_date());
        $html[] = '<br />';
        //TODO: date formatting
        $html[] = htmlentities(Translation :: get('To')) . ': ' . date('r', $event->get_end_date());
        $html[] = '</em>';
        $html[] = '<br />';
        $html[] = $event->get_description();
        return implode("\n", $html);
    }

    function as_html()
    {
        return parent :: as_html();
    }
}
?>