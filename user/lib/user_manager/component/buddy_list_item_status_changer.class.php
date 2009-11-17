<?php
/**
 * $Id: buddy_list_item_status_changer.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerBuddyListItemStatusChangerComponent extends UserManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(UserManager :: PARAM_BUDDYLIST_ITEM);
        $status = Request :: get('status');
        if ($id)
        {
            $udm = UserDataManager :: get_instance();
            
            $conditions[] = new EqualityCondition(BuddyListItem :: PROPERTY_BUDDY_ID, $this->get_user()->get_id());
            $conditions[] = new EqualityCondition(BuddyListItem :: PROPERTY_USER_ID, $id);
            $condition = new AndCondition($conditions);
            
            $buddy = $udm->retrieve_buddy_list_items($condition)->next_result();
            
            if ($buddy)
            {
                $buddy->set_status($status);
                $succes = $buddy->update();
                
                if ($succes && $status == 0)
                {
                    $buddy = new BuddyListItem();
                    $buddy->set_user_id($this->get_user_id());
                    $buddy->set_buddy_id($id);
                    $buddy->set_status(0);
                    $buddy->set_category_id(0);
                    $succes &= $buddy->create();
                }
            }
            
            if (! $succes)
                echo Translation :: get('StatusNotChanged');
            
            $ajax = Request :: get('ajax');
            if (! $ajax)
                $this->redirect(Translation :: get($succes ? 'StatusChanged' : 'StatusNotChanged'), ! $succes, array(Application :: PARAM_ACTION => UserManager :: ACTION_VIEW_BUDDYLIST));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>