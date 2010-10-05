<?php
/**
 * $Id: quota_creator.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';

require_once dirname(__FILE__) . '/../../quota.class.php';
require_once dirname(__FILE__) . '/../../forms/quota_form.class.php';
require_once dirname(__FILE__) . '/../../reservations_data_manager.class.php';

class ReservationsManagerQuotaCreatorComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTAS)), Translation :: get('ViewQuota')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateQuota')));
        
        $user = $this->get_user();
        
        if (! isset($user))
        {
            Display :: display_not_allowed($trail);
            exit();
        }
        
        $quota = new Quota();
        $form = new QuotaForm(QuotaForm :: TYPE_CREATE, $this->get_url(), $quota, $user);
        
        if ($form->validate())
        {
            $success = $form->create_quota();
            $this->redirect(Translation :: get($success ? 'QuotaCreated' : 'QuotaNotCreated'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTAS));
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