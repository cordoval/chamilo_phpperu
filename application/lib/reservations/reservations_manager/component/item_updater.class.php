<?php
/**
 * $Id: item_updater.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';

require_once dirname(__FILE__) . '/../../item.class.php';
require_once dirname(__FILE__) . '/../../forms/item_form.class.php';
require_once dirname(__FILE__) . '/../../reservations_data_manager.class.php';

class ReservationsManagerItemUpdaterComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_id = $_GET[ReservationsManager :: PARAM_CATEGORY_ID];
        $item_id = $_GET[ReservationsManager :: PARAM_ITEM_ID];
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS, ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('ViewItems')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ITEM_ID => $item_id, ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('UpdateItem')));
        
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
            $this->redirect(Translation :: get($success ? 'ItemUpdated' : 'ItemNotUpdated'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS, ReservationsManager :: PARAM_CATEGORY_ID => $category_id));
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