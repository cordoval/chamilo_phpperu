<?php

namespace application\reservations;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Display;
use common\libraries\Utilities;
/**
 * $Id: item_creator.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
class ReservationsManagerItemCreatorComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_id = $_GET[ReservationsManager :: PARAM_CATEGORY_ID];
//        $trail = BreadcrumbTrail :: get_instance();
//        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
//        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS, ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('ViewItems')));
//        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('CreateItem')));
        
        $user = $this->get_user();
        
        if (! isset($user))
        {
            Display :: display_not_allowed($trail);
            exit();
        }
        
        $item = new Item();
        $item->set_creator($this->get_user_id());
        $item->set_category(isset($category_id) ? $category_id : 0);
        
        $form = new ItemForm(ItemForm :: TYPE_CREATE, $this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), $item, $user);
        
        if ($form->validate())
        {
            $success = $form->create_item();
            $object = Translation :: get('Item');
            $message = $succes ? Translation :: get('ObjectCreated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES) :
                                 Translation :: get('ObjectNotCreated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES);

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