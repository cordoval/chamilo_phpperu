<?php
/**
 * $Id: webconference_deleter.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing.webconferencing_manager.component
 */
require_once dirname(__FILE__) . '/../webconferencing_manager.class.php';
require_once dirname(__FILE__) . '/../webconferencing_manager_component.class.php';

/**
 * Component to delete webconferences objects
 * @author Stefaan Vanbillemont
 */
class WebconferencingManagerWebconferenceDeleterComponent extends WebconferencingManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[WebconferencingManager :: PARAM_WEBCONFERENCE];
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $webconference = $this->retrieve_webconference($id);
                //Delete all webconference_options too
                //WebconferenceDataManager :: get_instance()->delete_webconference_options($webconference);
                

                if (! $webconference->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedWebconferenceDeleted';
                }
                else
                {
                    $message = 'SelectedWebconferenceDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedWebconferencesDeleted';
                }
                else
                {
                    $message = 'SelectedWebconferencesDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(WebconferencingManager :: PARAM_ACTION => WebconferencingManager :: ACTION_BROWSE_WEBCONFERENCES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoWebconferencesSelected')));
        }
    }
}
?>