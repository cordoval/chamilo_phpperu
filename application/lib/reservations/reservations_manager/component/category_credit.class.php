<?php
/**
 * $Id: category_credit.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';

require_once dirname(__FILE__) . '/../../category.class.php';
require_once dirname(__FILE__) . '/../../forms/credit_form.class.php';
require_once dirname(__FILE__) . '/../../reservations_data_manager.class.php';

class ReservationsManagerCategoryCreditComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_id = $_GET[ReservationsManager :: PARAM_CATEGORY_ID];
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('SetCredits')));
        
        $user = $this->get_user();
        
        if (! isset($user))
        {
            Display :: display_not_allowed($trail);
            exit();
        }
        
        $categories = $this->retrieve_categories(new EqualityCondition(Category :: PROPERTY_ID, $category_id));
        $category = $categories->next_result();
        
        if (! $category)
        {
            $category = new Category();
            $category->set_id($category_id);
        }
        
        $form = new CreditForm($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $category_id)));
        
        if ($form->validate())
        {
            $success = $form->set_credits_for_category($category);
            $this->redirect(Translation :: get($success ? 'CategoryCreditsApplied' : 'CategoryCreditsNotApplied'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS, ReservationsManager :: PARAM_CATEGORY_ID => $category->get_id()));
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