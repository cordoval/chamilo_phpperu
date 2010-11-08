<?php
namespace user;

use common\libraries\Request;

/**
 * $Id: buddy_list_item_deleter.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerBuddyListItemDeleterComponent extends UserManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(UserManager :: PARAM_BUDDYLIST_ITEM);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $buddy = new BuddyListItem();
                $buddy->set_user_id($this->get_user_id());
                $buddy->set_buddy_id($id);

                if (! $buddy->delete())
                {
                    $failures ++;
                }
            }

            if (! $failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectsDeleted';
                }
                else
                {
                    $message = 'ObjectDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectsNotDeleted';
                }
                else
                {
                    $message = 'ObjectNotDeleted';
                }
                echo $message;
            }

            $ajax = Request :: get('ajax');
            if (! $ajax)
                $this->redirect(Translation :: get($message, array('OBJECT' => Translation :: get('BuddyListItem')), Utilities :: COMMON_LIBRARIES), ($failures ? true : false), array(Application :: PARAM_ACTION => UserManager :: ACTION_VIEW_BUDDYLIST));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoBuddyListItemSelected')));
        }
    }
}
?>