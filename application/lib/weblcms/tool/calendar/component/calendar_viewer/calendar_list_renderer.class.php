<?php
/**
 * $Id: calendar_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.calendar.component.calendar_viewer
 */
require_once dirname(__FILE__) . '/../../../../browser/list_renderer/list_content_object_publication_list_renderer.class.php';
/**
 * A renderer to display a list view of a calendar
 */
class CalendarListRenderer extends ListContentObjectPublicationListRenderer
{

    function as_html()
    {
        $publications = $this->get_publications();
        if (count($publications) == 0)
        {
            $html[] = Display :: normal_message(Translation :: get('NoPublicationsAvailable'), true);
        }
        
        if ($this->get_actions() && $this->is_allowed(EDIT_RIGHT))
            $html[] = '<form name="publication_list" action="' . $this->get_url(array('view' => Request :: get('view'))) . '" method="GET" >';
        
        foreach ($publications as $index => $publication)
        {
            $object = $publication->get_content_object();
            
            if ($object->repeats())
            {
                $repeats = $object->get_repeats();
                
                foreach ($repeats as $repeat)
                {
                    $the_publication = clone $publication;
                    $the_publication->set_content_object($repeat);
                    
                    $rendered_publications[$publication->get_content_object()->get_start_date()][] = $this->render_publication($the_publication, false, false);
                }
            }
            else
            {
                $rendered_publications[$publication->get_content_object()->get_start_date()][] = $this->render_publication($publication, false, false);
            }
            
        //			$first = $index == 0;
        //			$last = $index == count($publications) - 1;
        //			$rendered_publications[$publication->get_content_object()->get_start_date()][] = $this->render_publication($publication, $first, $last);
        }
        ksort($rendered_publications);
        $current_month = 0;
        foreach ($rendered_publications as $start_time => $rendered_publication_start_time)
        {
            if (date('Ym', $start_time) != $current_month)
            {
                $current_month = date('Ym', $start_time);
                $html[] = '<h3>' . Translation :: get(date('F', $start_time) . 'Long') . ' ' . date('Y', $start_time) . '</h3>';
            }
            $html[] = implode("\n", $rendered_publication_start_time);
        }
        
        if ($this->get_actions() && count($publications) > 0 && $this->is_allowed(EDIT_RIGHT))
        {
            foreach ($_GET as $parameter => $value)
            {
                $html[] = '<input type="hidden" name="' . $parameter . '" value="' . $value . '" />';
            }
            
            $html[] = '<script type="text/javascript">
							/* <![CDATA[ */
							function setCheckbox(formName, value) {
								var d = document[formName];
								for (i = 0; i < d.elements.length; i++) {
									if (d.elements[i].type == "checkbox") {
									     d.elements[i].checked = value;
									}
								}
							}
							/* ]]> */
							</script>';
            
            $html[] = '<div style="text-align: right;">';
            $html[] = '<a href="?" onclick="setCheckbox(\'publication_list\', true); return false;">' . Translation :: get('SelectAll') . '</a>';
            $html[] = '- <a href="?" onclick="setCheckbox(\'publication_list\', false); return false;">' . Translation :: get('UnSelectAll') . '</a><br />';
            $html[] = '<select name="tool_action">';
            foreach ($this->get_actions() as $action => $label)
            {
                $html[] = '<option value="' . $action . '">' . $label . '</option>';
            }
            $html[] = '</select>';
            $html[] = ' <input type="submit" value="' . Translation :: get('Ok') . '"/>';
            $html[] = '</form>';
            $html[] = '</div>';
        }
        return implode("\n", $html);
    }

    /**
     * Render the description of the calendar event publication
     */
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

    /**
     * Calendar events are sorted chronologically. So the up-action is not
     * available here.
     * @return empty string
     */
    function render_up_action()
    {
        return '';
    }

    /**
     * Calendar events are sorted chronologically. So the down-action is not
     * available here.
     * @return empty string
     */
    function render_down_action()
    {
        return '';
    }

    /**
     * No categories available in the calendar tool at this moment, so the
     * option to move calendar events between categories is not available.
     * @return empty string
     */
    function render_move_to_category_action($publication)
    {
        return '';
    }
}
?>