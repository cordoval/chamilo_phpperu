<?php
/**
 * $Id: quota_box_deleter.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';
require_once dirname(__FILE__) . '/../../quota_box.class.php';
require_once dirname(__FILE__) . '/../reservations_manager_component.class.php';

/**
 * Component to delete a category
 */
class ReservationsManagerQuotaBoxDeleterComponent extends ReservationsManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(ReservationsManager :: PARAM_QUOTA_BOX_ID);
        
        if (! $this->get_user())
        {
            $this->display_header(null);
            Display :: display_error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        if ($ids)
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            $bool = true;
            
            foreach ($ids as $id)
            {
                $box = new QuotaBox();
                $box->set_id($id);
                
                if (! $box->delete())
                {
                    $bool = false;
                }
                else
                {
                    Events :: trigger_event('delete_quota_box', 'reservations', array('target_id' => $id, 'user_id' => $this->get_user_id()));
                }
            
            }
            
            if (count($ids) == 1)
                $message = $bool ? 'QuotaBoxDeleted' : 'QuotaBoxNotDeleted';
            else
                $message = $bool ? 'QuotaBoxesDeleted' : 'QuotaBoxesNotDeleted';
            
            $this->redirect(Translation :: get($message), ($bool ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTA_BOXES));
        }
        else
        {
            $this->display_header();
            $this->display_error_message(Translation :: get("NoObjectSelected"));
            $this->display_footer();
        }
    }

}
?>