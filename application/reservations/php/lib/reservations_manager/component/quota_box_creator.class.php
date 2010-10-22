<?php

namespace application\reservations;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Display;
/**
 * $Id: quota_box_creator.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
class ReservationsManagerQuotaBoxCreatorComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_id = $_GET[ReservationsManager :: PARAM_CATEGORY_ID];
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTA_BOXES)), Translation :: get('ViewQuotaBoxes')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateQuotaBox')));
        
        $user = $this->get_user();
        
        if (! isset($user))
        {
            Display :: display_not_allowed($trail);
            exit();
        }
        
        $quota_box = new QuotaBox();
        
        $form = new QuotaBoxForm(QuotaBoxForm :: TYPE_CREATE, $this->get_url(), $quota_box, $user);
        
        if ($form->validate())
        {
            $success = $form->create_quota_box();
            $this->redirect(Translation :: get($success ? 'QuotaBoxCreated' : 'QuotaBoxNotCreated'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTA_BOXES));
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