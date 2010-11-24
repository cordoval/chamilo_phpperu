<?php

namespace application\reservations;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Display;
use common\libraries\EqualityCondition;
use common\libraries\Utilities;
/**
 * $Id: quota_updater.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.reservations_manager.component
 */
class ReservationsManagerQuotaUpdaterComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $quota_id = $_GET[ReservationsManager :: PARAM_QUOTA_ID];
        $trail = BreadcrumbTrail :: get_instance();
//        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
//        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTAS)), Translation :: get('ViewQuota')));
//        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateQuota')));
        
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
            $object = Translation :: get('Quota');
            $message = $success ? Translation :: get('ObjectUpdated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES) :
                                  Translation :: get('ObjectNotUpdated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES);

            $this->redirect($message, !$success, array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTAS));
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