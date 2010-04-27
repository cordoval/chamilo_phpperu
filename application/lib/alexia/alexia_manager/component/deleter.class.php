<?php

/**
 * $Id: deleter.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexia.alexia_manager.component
 */

require_once dirname(__FILE__) . '/../alexia_manager.class.php';

class AlexiaManagerDeleterComponent extends AlexiaManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(AlexiaManager :: PARAM_ALEXIA_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $publication = $this->get_parent()->retrieve_alexia_publication($id);
                
                if (! $publication->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPublicationNotDeleted';
                }
                else
                {
                    $message = 'SelectedPublicationsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPublicationDeleted';
                }
                else
                {
                    $message = 'SelectedPublicationsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_BROWSE_PUBLICATIONS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoPublicationSelected')));
        }
    }
}
?>