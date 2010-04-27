<?php
/**
 * $Id: category_blackout.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';


/**
 * Component to delete a category
 */
class ReservationsManagerCategoryBlackoutComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = $_GET[ReservationsManager :: PARAM_CATEGORY_ID];
        $blackout = $_GET[ReservationsManager :: PARAM_BLACKOUT];
        
        if (! $this->get_user())
        {
            $this->display_header(null);
            Display :: display_error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        if (isset($id) && isset($blackout))
        {
            $bool = $this->blackout_category($id, $blackout);
            
            $message = $bool ? 'BlackoutSuccesFull' : 'BlackoutFailed';
            
            $message = $blackout ? $message : 'Un' . $message;
            
            $this->redirect(Translation :: get($message), ($bool ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS, ReservationsManager :: PARAM_CATEGORY_ID => $id));
        }
        else
        {
            $this->display_header();
            $this->display_error_message(Translation :: get("NoObjectSelected"));
            $this->display_footer();
        }
    }

    function blackout_category($cat_id, $blackout)
    {
        $bool = true;
        
        $items = $this->retrieve_items(new EqualityCondition(Item :: PROPERTY_CATEGORY, $cat_id));
        while ($item = $items->next_result())
        {
            $item->set_blackout($blackout);
            $bool = $bool & $item->update();
        }
        
        $categories = $this->retrieve_categories(new EqualityCondition(Category :: PROPERTY_PARENT, $cat_id));
        while ($category = $categories->next_result())
        {
            $bool = $bool & $this->blackout_category($category->get_id(), $blackout);
        }
        
        return $bool;
    }

}
?>