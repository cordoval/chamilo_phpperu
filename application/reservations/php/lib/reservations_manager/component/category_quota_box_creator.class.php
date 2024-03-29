<?php

namespace application\reservations;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Display;
use common\libraries\Request;
use common\libraries\Utilities;
/**
 * $Id: category_quota_box_creator.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
class ReservationsManagerCategoryQuotaBoxCreatorComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_id = $this->get_category_id();
        
        $trail = BreadcrumbTrail :: get_instance();
//        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
//        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_CATEGORY_QUOTA_BOXES, ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('ViewCategoryQuotaBoxes')));
//        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('CreateCategoryQuotaBox')));
        
        $user = $this->get_user();
        
        if (! isset($user))
        {
            Display :: display_not_allowed($trail);
            exit();
        }
        
        $quota_box_rel_category = new QuotaBoxRelCategory();
        $quota_box_rel_category->set_category_id($category_id);
        
        $form = new CategoryQuotaBoxForm(CategoryQuotaBoxForm :: TYPE_CREATE, $this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), $quota_box_rel_category, $user);
        
        if ($form->validate())
        {
            $success = $form->create_quota_box_rel_category();

            $quotabox = Translation :: get('QuotaBox');
            $message = $succes ? Translation :: get('ObjectAdded', array('OBJECT' => $quotabox), Utilities :: COMMON_LIBRARIES) :
                                 Translation :: get('ObjectNotAdded', array('OBJECT' => $quotabox), Utilities :: COMMON_LIBRARIES);

            $this->redirect($message, !$success, array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_CATEGORY_QUOTA_BOXES, ReservationsManager :: PARAM_CATEGORY_ID => $category_id));
        }
        else
        {
            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
    }

    function get_category_id()
    {
        $id = Request :: get(ReservationsManager :: PARAM_CATEGORY_ID);
        if (! isset($id) || is_null($id))
            $id = 0;
        
        return $id;
    }
}
?>