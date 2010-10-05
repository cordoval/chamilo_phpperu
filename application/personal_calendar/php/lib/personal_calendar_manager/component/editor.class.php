<?php
/**
 * $Id: editor.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.personal_calendar_manager.component
 */
require_once dirname(__FILE__) . '/../personal_calendar_manager.class.php';
require_once dirname(__FILE__) . '/../../renderer/personal_calendar_mini_month_renderer.class.php';
require_once dirname(__FILE__) . '/../../personal_calendar_publication_form.class.php';

class PersonalCalendarManagerEditorComponent extends PersonalCalendarManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $user = $this->get_user();
        
        $id = Request :: get(PersonalCalendarManager :: PARAM_PERSONAL_CALENDAR_ID);
        
        if ($id)
        {
            $calendar_event_publication = $this->retrieve_personal_calendar_publication($id);
            
            if (! $user->is_platform_admin() && $calendar_event_publication->get_publisher() != $user->get_id())
            {
                $this->display_header();
                $this->display_error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            
            $content_object = $calendar_event_publication->get_publication_object();
            
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_EDIT_PUBLICATION, PersonalCalendarManager :: PARAM_PERSONAL_CALENDAR_ID => $calendar_event_publication->get_id())));
            
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
                
                $publication_form = new PersonalCalendarPublicationForm(PersonalCalendarPublicationForm :: TYPE_SINGLE, $content_object, $user, $this->get_url(array(PersonalCalendarManager :: PARAM_PERSONAL_CALENDAR_ID => $calendar_event_publication->get_id(), 'validated' => 1)));
                $publication_form->set_publication($calendar_event_publication);
                
                if ($publication_form->validate())
                {
                    $success = $publication_form->update_calendar_event_publication();
                    $this->redirect(Translation :: get(($success ? 'PersonalCalendarPublicationUpdated' : 'PersonalCalendarPublicationNotUpdated')), ($success ? false : true), array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR));
                }
                else
                {
                    $this->display_header(null, true);
                    $publication_form->display();
                    $this->display_footer();
                }
            }
            else
            {
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoPersonalCalendarPublicationSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR)), Translation :: get('PersonalCalendarManagerBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_VIEW_PUBLICATION, self :: PARAM_PERSONAL_CALENDAR_ID => Request :: get(self :: PARAM_PERSONAL_CALENDAR_ID))), Translation :: get('PersonalCalendarManagerViewerComponent')));
    	$breadcrumbtrail->add_help('personal_calendar_editor');
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_PERSONAL_CALENDAR_ID);
    }
}
?>