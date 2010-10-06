<?php
/**
 * $Id: personal_calendar_list_renderer.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.renderer
 */
require_once WebApplication :: get_application_class_lib_path('personal_calendar') . 'personal_calendar_renderer.class.php';
/**
 * This personal calendar renderer provides a simple list view of the events in
 * the calendar.
 */
class PersonalCalendarListRenderer extends PersonalCalendarRenderer
{

    /**
     * @see PersonalCalendarRenderer::render()
     */
    public function render()
    {
        // Range from start (0) to 10 years in the future...
        

        $events = $this->get_events(0, strtotime('+10 Years', time()));
        $dm = RepositoryDataManager :: get_instance();
        $html = array();
        
        if (count($events) == 0)
        {
            $this->get_parent()->display_message(Translation :: get('NoPublications'));
        }
        
        foreach ($events as $index => $event)
        {
            $html[$event->get_start_date()][] = $this->render_event($event);
        }
        ksort($html);
        $out = '';
        foreach ($html as $time => $content)
        {
            $out .= implode("\n", $content);
        }
        return $out;
    }

    function render_event($event)
    {
        $html = array();
        $date_format = Translation :: get('dateTimeFormatLong');
        
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/calendar_event.png);">';
        $html[] = '<div class="title">' . htmlentities($event->get_title()) . '</div>';
        $html[] = '<div class="description">';
        if ($event->get_end_date() != '')
        {
            $html[] = '<div class="calendar_event_range">' . htmlentities(Translation :: get('From') . ' ' . DatetimeUtilities :: format_locale_date($date_format, $event->get_start_date()) . ' ' . Translation :: get('Until') . ' ' . DatetimeUtilities :: format_locale_date($date_format, $event->get_end_date())) . '</div>';
        }
        else
        {
            $html[] = '<div class="calendar_event_range">' . DatetimeUtilities :: format_locale_date($date_format, $event->get_start_date()) . '</div>';
        }
        $html[] = $event->get_content();
        $html[] = $this->render_attachments($event);
        $html[] = '</div>';
        if ($event->get_source() == Utilities :: underscores_to_camelcase(CalendarEvent :: get_type_name()))
        {
            $html[] = '<div style="float: right;">';
            $html[] = $this->get_publication_actions($event);
            $html[] = '</div>';
        }
        else
        {
            $html[] = '<div style="float: right;">';
            $html[] = $this->get_external_publication_actions($event);
            $html[] = '</div>';
        }
        //        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        return implode("\n", $html);
    }

    function get_publication_actions($event)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('View'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_VIEW_PUBLICATION, PersonalCalendarManager :: PARAM_PERSONAL_CALENDAR_ID => $event->get_id())), ToolbarItem :: DISPLAY_ICON));
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_parent()->get_publication_editing_url($event), ToolbarItem :: DISPLAY_ICON));
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_parent()->get_publication_deleting_url($event), ToolbarItem :: DISPLAY_ICON, true));
        
        return $toolbar->as_html();
    }

    function get_external_publication_actions($event)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('View'), Theme :: get_common_image_path() . 'action_browser.png', html_entity_decode($event->get_url()), ToolbarItem :: DISPLAY_ICON));
        
        return $toolbar->as_html();
    }

    function render_attachments($event)
    {
        if (is_null($event->get_id()))
            return;
        
        if ($event->get_source() == 'weblcms')
        {
            $publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($event->get_id());
            $object = $publication->get_content_object();
        }
        
        elseif($event->get_source() == 'internship_organizer_moment')
        {
            $object = InternshipOrganizerDataManager :: get_instance()->retrieve_moment($event->get_id());
            
        }else      
        {
            $publication = PersonalCalendarDataManager :: get_instance()->retrieve_personal_calendar_publication($event->get_id());
            $object = $publication->get_publication_object();
        }
        
        if ($object instanceof AttachmentSupport)
        {
            $attachments = $object->get_attached_content_objects();
            if (count($attachments) > 0)
            {
                $html[] = '<div class="attachments" style="margin-top: 1em;">';
                $html[] = '<div class="attachments_title">' . htmlentities(Translation :: get('Attachments')) . '</div>';
                Utilities :: order_content_objects_by_title($attachments);
                $html[] = '<ul class="attachments_list">';
                foreach ($attachments as $attachment)
                {
                    $html[] = '<li><a href="' . $this->get_parent()->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_VIEW_ATTACHMENT, 'object' => $attachment->get_id())) . '"><img src="' . Theme :: get_common_image_path() . 'treemenu_types/' . $attachment->get_type() . '.png" alt="' . htmlentities(Translation :: get(ContentObject :: type_to_class($attachment->get_type()) . 'TypeName')) . '"/> ' . $attachment->get_title() . '</a></li>';
                }
                $html[] = '</ul></div>';
                return implode("\n", $html);
            }
        }
        return '';
    }
}
?>