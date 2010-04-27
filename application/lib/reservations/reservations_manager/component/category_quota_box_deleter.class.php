<?php
/**
 * $Id: category_quota_box_deleter.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';
require_once dirname(__FILE__) . '/../../quota_box.class.php';


/**
 * Component to delete a category
 */
class ReservationsManagerCategoryQuotaBoxDeleterComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(ReservationsManager :: PARAM_CATEGORY_QUOTA_BOX_ID);
        $cat_id = Request :: get(ReservationsManager :: PARAM_CATEGORY_ID);
        
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
            
            $rdm = ReservationsDataManager :: get_instance();
            
            foreach ($ids as $id)
            {
                /*$box = new QuotaBoxRelCategory();
    			$box->set_id($id);
    			$bool &= $box->delete();*/
                
                $box = $rdm->retrieve_quota_box_rel_categories(new EqualityCondition(QuotaBoxRelCategory :: PROPERTY_ID, $id))->next_result();
                $category = $box->get_category_id();
                $quota_box = $box->get_quota_box_id();
                
                if (! $box->delete())
                {
                    $bool = false;
                }
                else
                {
                    Events :: trigger_event('delete_quota_box_category', 'reservations', array('target_id' => $id, 'user_id' => $this->get_user_id()));
                }
                
                $subcats = Category :: retrieve_sub_categories($category, true);
                foreach ($subcats as $subcat)
                {
                    $conditions = array();
                    $conditions[] = new EqualityCondition(QuotaBoxRelCategory :: PROPERTY_CATEGORY_ID, $subcat->get_id());
                    $conditions[] = new EqualityCondition(QuotaBoxRelCategory :: PROPERTY_QUOTA_BOX_ID, $quota_box);
                    $condition = new AndCondition($conditions);
                    
                    $box = $rdm->retrieve_quota_box_rel_categories($condition)->next_result();
                    if (! box)
                        break;
                    
                    if (! $box->delete())
                    {
                        $bool = false;
                    }
                    else
                    {
                        Events :: trigger_event('delete_quota_box', 'reservations', array('target_id' => $box->get_id(), 'user_id' => $this->get_user_id()));
                    }
                }
            }
            
            if (count($ids) == 1)
                $message = $bool ? 'CategoryQuotaBoxDeleted' : 'CategoryQuotaBoxNotDeleted';
            else
                $message = $bool ? 'CategoryQuotaBoxesDeleted' : 'CategoryQuotaBoxesNotDeleted';
            
            $this->redirect(Translation :: get($message), ($bool ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_CATEGORY_QUOTA_BOXES, ReservationsManager :: PARAM_CATEGORY_ID => $cat_id));
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