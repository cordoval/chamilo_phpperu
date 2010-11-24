<?php

namespace application\reservations;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Display;
use common\libraries\EqualityCondition;
use common\libraries\Utilities;
/**
 * $Id: item_updater.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
class ReservationsManagerItemUpdaterComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_id = $_GET[ReservationsManager :: PARAM_CATEGORY_ID];
        $item_id = $_GET[ReservationsManager :: PARAM_ITEM_ID];
        
//        $trail = BreadcrumbTrail :: get_instance();
//        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
//        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS, ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('ViewItems')));
//        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ITEM_ID => $item_id, ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('UpdateItem')));
        
        $user = $this->get_user();
        
        if (! isset($user))
        {
            Display :: display_not_allowed($trail);
            exit();
        }
        
        $items = $this->retrieve_items(new EqualityCondition(Item :: PROPERTY_ID, $item_id));
        $item = $items->next_result();
        
        $form = new ItemForm(ItemForm :: TYPE_EDIT, $this->get_url(array(ReservationsManager :: PARAM_ITEM_ID => $item->get_id(), ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), $item, $user);
        
        if ($form->validate())
        {
            $success = $form->update_item();
            $object = Translation :: get('Item');
            $message = $succes ? Translation :: get('ObjectUpdated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES):
                                 Translation :: get('ObjectNotUpdated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES);

            $this->redirect($message, !$success, array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS, ReservationsManager :: PARAM_CATEGORY_ID => $category_id));
        }
        else
        {
            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
    }
}
?>