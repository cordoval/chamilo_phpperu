<?php
/**
 * $Id: deleter.class.php 199 2009-11-13 12:23:04Z chellee $
 * @package application.lib.linker.linker_manager.component
 */
require_once WebApplication :: get_application_class_lib_path('linker') . 'linker_manager/linker_manager.class.php';

class LinkerManagerDeleterComponent extends LinkerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(LinkerManager :: PARAM_LINK_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $link = $this->retrieve_link($id);
                
                if (! $link->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedLinkDeleted';
                }
                else
                {
                    $message = 'SelectedLinkDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedLinksDeleted';
                }
                else
                {
                    $message = 'SelectedLinksDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => LinkerManager :: ACTION_BROWSE_LINKS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoLinksSelected')));
        }
    }
}
?>