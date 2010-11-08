<?php

namespace application\personal_messenger;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Application;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
/**
 * $Id: deleter.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class PersonalMessengerManagerDeleterComponent extends PersonalMessengerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(PersonalMessengerManager :: PARAM_PERSONAL_MESSAGE_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $publication = $this->retrieve_personal_message_publication($id);
                
                if (! $publication->delete())
                {
                    $failures ++;
                }
            }
            
     	    if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get('ObjectNotDeleted',array('OBJECT' => Translation :: get('PersonalMessengerPublication')), Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get('ObjectsNotDeleted',array('OBJECT' => Translation :: get('PersonalMessengerPublications')), Utilities :: COMMON_LIBRARIES);
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get('ObjectDeleted',array('OBJECT' => Translation :: get('PersonalMessengerPublication')), Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get('ObjectsDeleted',array('OBJECT' => Translation :: get('PersonalMessengerPublications')), Utilities :: COMMON_LIBRARIES);
                }
            }
                  
            $this->redirect($message, ($failures ? true : false), array(Application :: PARAM_ACTION => PersonalMessengerManager :: ACTION_BROWSE_MESSAGES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected', null , Utilities :: COMMON_LIBRARIES)));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_MESSAGES)), Translation :: get('PersonalMessengerManagerBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_VIEW_PUBLICATION, self :: PARAM_PERSONAL_MESSAGE_ID => Request :: get(self :: PARAM_PERSONAL_MESSAGE_ID))), Translation :: get('PersonalMessengerManagerViewerComponent')));
    	$breadcrumbtrail->add_help('personal_messenger_deleter');
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_PERSONAL_MESSAGE_ID);
    }
}
?>