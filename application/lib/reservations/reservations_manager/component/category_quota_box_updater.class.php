<?php
/**
 * $Id: category_quota_box_updater.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';
require_once dirname(__FILE__) . '/../reservations_manager_component.class.php';
require_once dirname(__FILE__) . '/../../quota_box.class.php';
require_once dirname(__FILE__) . '/../../forms/category_quota_box_form.class.php';
require_once dirname(__FILE__) . '/../../reservations_data_manager.class.php';

class ReservationsManagerCategoryQuotaBoxUpdaterComponent extends ReservationsManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_quota_box_id = Request :: get(ReservationsManager :: PARAM_CATEGORY_QUOTA_BOX_ID);
        $quota_box_rel_categories = $this->retrieve_quota_box_rel_categories(new EqualityCondition(QuotaBoxRelCategory :: PROPERTY_ID, $category_quota_box_id));
        $quota_box_rel_category = $quota_box_rel_categories->next_result();
        
        $category_id = $quota_box_rel_category->get_category_id();
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_CATEGORIES)), Translation :: get('ManageCategories')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_CATEGORY_QUOTA_BOXES, ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('ViewCategoryQuotaBoxes')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_QUOTA_BOX_ID => $category_quota_box_id)), Translation :: get('UpdateCategoryQuotaBox')));
        
        $user = $this->get_user();
        
        if (! isset($user))
        {
            Display :: display_not_allowed($trail);
            exit();
        }
        
        $form = new CategoryQuotaBoxForm(CategoryQuotaBoxForm :: TYPE_EDIT, $this->get_url(array(ReservationsManager :: PARAM_CATEGORY_QUOTA_BOX_ID => $category_quota_box_id)), $quota_box_rel_category, $user);
        
        if ($form->validate())
        {
            $success = $form->update_quota_box_rel_category();
            $this->redirect(Translation :: get($success ? 'CategoryQuotaBoxUpdated' : 'CategoryQuotaBoxNotUpdated'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_CATEGORY_QUOTA_BOXES, ReservationsManager :: PARAM_CATEGORY_ID => $category_id));
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