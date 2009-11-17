<?php
/**
 * $Id: category_quota_box_creator.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';
require_once dirname(__FILE__) . '/../reservations_manager_component.class.php';
require_once dirname(__FILE__) . '/../../quota_box_rel_category.class.php';
require_once dirname(__FILE__) . '/../../forms/category_quota_box_form.class.php';
require_once dirname(__FILE__) . '/../../reservations_data_manager.class.php';

class ReservationsManagerCategoryQuotaBoxCreatorComponent extends ReservationsManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_id = $this->get_category_id();
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_CATEGORY_QUOTA_BOXES, ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('ViewCategoryQuotaBoxes')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('CreateCategoryQuotaBox')));
        
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
            $this->redirect(Translation :: get($success ? 'QuotaBoxAdded' : 'QuotaBoxNotAdded'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_CATEGORY_QUOTA_BOXES, ReservationsManager :: PARAM_CATEGORY_ID => $category_id));
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