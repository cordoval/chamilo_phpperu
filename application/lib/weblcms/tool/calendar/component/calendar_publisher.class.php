<?php
/**
 * $Id: calendar_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.calendar.component
 */
require_once dirname(__FILE__) . '/../calendar_tool.class.php';
require_once dirname(__FILE__) . '/../calendar_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/content_object_publisher.class.php';

class CalendarToolPublisherComponent extends CalendarToolComponent
{

    function run()
    {
        if (! $this->is_allowed(ADD_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => CalendarTool :: ACTION_PUBLISH)), Translation :: get('Publish')));
        $trail->add_help('courses calendar tool');

        $event = new CalendarEvent();
        $event->set_owner_id($this->get_user_id());
        $event->set_start_date(intval(Request :: get('default_start_date')));
        $event->set_end_date(intval(Request :: get('default_end_date')));
        
        $pub = new ContentObjectRepoViewer($this, CalendarEvent :: get_type_name());
        $pub->set_default_content_object(CalendarEvent :: get_type_name(), $event);
        
        if (!$pub->any_object_selected())
        {
            $html[] = $pub->as_html();
        }
        else
        {
            //$html[] = 'ContentObject: ';
            $publisher = new ContentObjectPublisher($pub);
            $html[] = $publisher->get_publications_form($pub->get_selected_objects());
        }
        
        $this->display_header($trail, true);
        echo implode("\n", $html);
        $this->display_footer();
    }
}
?>