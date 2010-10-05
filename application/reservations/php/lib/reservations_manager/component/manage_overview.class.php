<?php
/*/**
 * $Id: manage_overview.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';

require_once dirname(__FILE__) . '/../../overview_item.class.php';
require_once dirname(__FILE__) . '/../../forms/overview_item_form.class.php';
require_once dirname(__FILE__) . '/../../reservations_data_manager.class.php';

class ReservationsManagerManageOverviewComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_OVERVIEW)), Translation :: get('Statistics')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ManageItems')));
        
        $user = $this->get_user();
        
        if (! isset($user))
        {
            Display :: display_not_allowed($trail);
            exit();
        }
        
        $form = new OverviewItemForm($this->get_url(), $user);
        
        if ($form->validate())
        {
            $success = $form->update_overview();
            $this->redirect(Translation :: get($success ? 'StatisticsListUpdated' : 'StatisticsListNotUpdated'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_OVERVIEW));
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