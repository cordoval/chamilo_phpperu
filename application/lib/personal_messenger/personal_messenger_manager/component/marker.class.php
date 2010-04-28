<?php
/**
 * $Id: marker.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

require_once dirname(__FILE__) . '/../personal_messenger_manager.class.php';

class PersonalMessengerManagerMarkerComponent extends PersonalMessengerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(PersonalMessengerManager :: PARAM_PERSONAL_MESSAGE_ID);
        $mark_type = Request :: get(PersonalMessengerManager :: PARAM_MARK_TYPE);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $publication = $this->get_parent()->retrieve_personal_message_publication($id);
                if ($mark_type == PersonalMessengerManager :: PARAM_MARK_SELECTED_READ)
                {
                    $publication->set_status(0);
                }
                elseif ($mark_type == PersonalMessengerManager :: PARAM_MARK_SELECTED_UNREAD)
                {
                    $publication->set_status(1);
                }
                
                if (! $publication->update())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPublicationNotUpdated';
                }
                else
                {
                    $message = 'SelectedPublicationsNotUpdated';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPublicationUpdated';
                }
                else
                {
                    $message = 'SelectedPublicationsUpdated';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => PersonalMessengerManager :: ACTION_BROWSE_MESSAGES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoPublicationSelected')));
        }
    }
}
?>