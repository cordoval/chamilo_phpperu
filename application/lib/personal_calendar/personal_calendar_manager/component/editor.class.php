<?php
/**
 * $Id: editor.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.personal_calendar_manager.component
 */
require_once dirname(__FILE__) . '/../personal_calendar_manager.class.php';
require_once dirname(__FILE__) . '/../personal_calendar_manager_component.class.php';
require_once dirname(__FILE__) . '/../../renderer/personal_calendar_mini_month_renderer.class.php';
require_once dirname(__FILE__) . '/../../calendar_event_publication_form.class.php';

class PersonalCalendarManagerEditorComponent extends PersonalCalendarManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $user = $this->get_user();
        
        $id = Request :: get(PersonalCalendarManager :: PARAM_CALENDAR_EVENT_ID);
        
        if ($id)
        {
            $calendar_event_publication = $this->retrieve_calendar_event_publication($id);
            
            if (! $user->is_platform_admin() && $calendar_event_publication->get_publisher() != $user->get_id())
            {
                $this->display_header($trail);
                $this->display_error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            
            $content_object = $calendar_event_publication->get_publication_object();
            
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR)), Translation :: get('PersonalCalendar')));
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_VIEW_PUBLICATION, PersonalCalendarManager :: PARAM_CALENDAR_EVENT_ID => $id)), $content_object->get_title()));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Edit')));
            $trail->add_help('personal calender general');
            
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_EDIT_PUBLICATION, PersonalCalendarManager :: PARAM_CALENDAR_EVENT_ID => $calendar_event_publication->get_id())));
            
            if ($form->validate() || Request :: get('validated'))
            {
                if (! Request :: get('validated'))
                {
                    $success = $form->update_content_object();
                }
                
                if ($form->is_version())
                {
                    $calendar_event_publication->set_content_object($content_object->get_latest_version());
                    $calendar_event_publication->update();
                }
                
                $publication_form = new CalendarEventPublicationForm(CalendarEventPublicationForm :: TYPE_SINGLE, $content_object, $user, $this->get_url(array(PersonalCalendarManager :: PARAM_CALENDAR_EVENT_ID => $calendar_event_publication->get_id(), 'validated' => 1)));
                $publication_form->set_publication($calendar_event_publication);
                
                if ($publication_form->validate())
                {
                    $success = $publication_form->update_calendar_event_publication();
                    $this->redirect(Translation :: get(($success ? 'CalendarEventPublicationUpdated' : 'CalendarEventPublicationNotUpdated')), ($success ? false : true), array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR));
                }
                else
                {
                    $this->display_header($trail, true);
                    $publication_form->display();
                    $this->display_footer();
                }
            }
            else
            {
                $this->display_header($trail);
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoCalendarEventPublicationSelected')));
        }
    }
}
?>