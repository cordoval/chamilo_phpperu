<?php
/**
 * $Id: quota_updater.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';

require_once dirname(__FILE__) . '/../../quota.class.php';
require_once dirname(__FILE__) . '/../../forms/quota_form.class.php';
require_once dirname(__FILE__) . '/../../reservations_data_manager.class.php';

class ReservationsManagerQuotaUpdaterComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $quota_id = $_GET[ReservationsManager :: PARAM_QUOTA_ID];
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTAS)), Translation :: get('ViewQuota')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateQuota')));
        
        $user = $this->get_user();
        
        if (! isset($user))
        {
            Display :: display_not_allowed($trail);
            exit();
        }
        
        $quotas = $this->retrieve_quotas(new EqualityCondition(Quota :: PROPERTY_ID, $quota_id));
        $quota = $quotas->next_result();
        
        $form = new QuotaForm(QuotaForm :: TYPE_EDIT, $this->get_url(array(ReservationsManager :: PARAM_QUOTA_ID => $quota->get_id())), $quota, $user);
        
        if ($form->validate())
        {
            $success = $form->update_quota();
            $this->redirect(Translation :: get($success ? 'QuotaUpdated' : 'QuotaNotUpdated'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTAS));
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