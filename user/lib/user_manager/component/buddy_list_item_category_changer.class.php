<?php
/**
 * $Id: buddy_list_item_category_changer.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerBuddyListItemCategoryChangerComponent extends UserManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: post('buddy');
        $new_category = Request :: post('new_category');
        if ($id && isset($new_category))
        {
            $udm = UserDataManager :: get_instance();
            
            $conditions[] = new EqualityCondition(BuddyListItem :: PROPERTY_USER_ID, $this->get_user()->get_id());
            $conditions[] = new EqualityCondition(BuddyListItem :: PROPERTY_BUDDY_ID, $id);
            $condition = new AndCondition($conditions);
            
            $buddy = $udm->retrieve_buddy_list_items($condition)->next_result();
            if ($buddy)
            {
                $buddy->set_category_id($new_category);
                $succes = $buddy->update();
            }
        }
        else
        {
            echo Translation :: get('NoObjectSelected');
        }
    }
}
?>