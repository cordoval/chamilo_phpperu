<?php
namespace application\weblcms\tool\calendar;

use common\libraries\Translation;
use common\libraries\Utilities;
use application\weblcms\ContentObjectPublicationDetailsRenderer;

/**
 * $Id: calendar_details_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.calendar.component.calendar_viewer
 */
class CalendarDetailsRenderer extends ContentObjectPublicationDetailsRenderer
{

    function __construct($browser)
    {
        parent :: __construct($browser);
    }

    function render_description($publication)
    {
        $event = $publication->get_content_object();
        $html[] = '<em>';
        //TODO: date formatting
        $html[] = htmlentities(Translation :: get('From', null , Utilities :: COMMON_LIBRARIES)) . ': ' . date('r', $event->get_start_date());
        $html[] = '<br />';
        //TODO: date formatting
        $html[] = htmlentities(Translation :: get('To', null , Utilities :: COMMON_LIBRARIES)) . ': ' . date('r', $event->get_end_date());
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