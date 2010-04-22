<?php
/**
 * $Id: system_announcement_hider.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */

class AdminManagerSystemAnnouncementHiderComponent extends AdminManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(AdminManager :: PARAM_SYSTEM_ANNOUNCEMENT_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $publication = $this->get_parent()->retrieve_system_announcement_publication($id);
                
                $publication->toggle_visibility();
                if (! $publication->update())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPublicationVisibilityNotToggled';
                }
                else
                {
                    $message = 'SelectedPublicationsVisibilityNotToggled';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPublicationVisibilityToggled';
                }
                else
                {
                    $message = 'SelectedPublicationsVisibilityToggled';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => AdminManager :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoPublicationSelected')));
        }
    }
}
?>