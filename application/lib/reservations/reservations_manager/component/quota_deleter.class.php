<?php
/**
 * $Id: quota_deleter.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';


/**
 * Component to delete a quota
 */
class ReservationsManagerQuotaDeleterComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[ReservationsManager :: PARAM_QUOTA_ID];
        
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
                /*$quotas = $this->retrieve_quotas(new EqualityCondition(Quota :: PROPERTY_ID, $id));
    			$quota = $quotas->next_result();*/
                $quota = new Quota();
                $quota->set_id($id);
                
                if (! $quota->delete())
                {
                    $bool = false;
                }
                else
                {
                    Events :: trigger_event('delete_quota', 'reservations', array('target_id' => $id, 'user_id' => $this->get_user_id()));
                }
            }
            
            if (count($ids) == 1)
                $message = $bool ? 'QuotasDeleted' : 'QuotasNotDeleted';
            else
                $message = $bool ? 'QuotasDeleted' : 'QuotasNotDeleted';
            
            $this->redirect(Translation :: get($message), ($bool ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTAS));
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