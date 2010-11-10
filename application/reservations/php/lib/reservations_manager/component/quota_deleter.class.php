<?php

namespace application\reservations;

use common\libraries\Display;
use common\libraries\Translation;
use tracking\Event;
use tracking\ChangesTracker;
use common\libraries\Utilities;
/**
 * $Id: quota_deleter.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.reservations_manager.component
 */
/**
 * Component to delete a quota
 */
class ReservationsManagerQuotaDeleterComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[ReservationsManager :: PARAM_QUOTA_ID];

        if (! $this->get_user())
        {
            $this->display_header(null);
            Display :: display_error_message(Translation :: get('NotAllowed', null, Utilities :: COMMON_LIBRARIES));
            $this->display_footer();
            exit();
        }

        if ($ids)
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            $bool = true;

            foreach ($ids as $id)
            {
                /*$quotas = $this->retrieve_quotas(new EqualityCondition(Quota :: PROPERTY_ID, $id));
    			$quota = $quotas->next_result();*/
                $quota = new Quota();
                $quota->set_id($id);

                if (! $quota->delete())
                {
                    $bool = false;
                }
                else
                {
                    Event :: trigger('delete_quota', ReservationsManager :: APPLICATION_NAME, array(ChangesTracker :: PROPERTY_REFERENCE_ID => $id, ChangesTracker :: PROPERTY_USER_ID => $this->get_user_id()));
                }
            }

            if (count($ids) == 1)
            {
                $object = Translation :: get('Quota');
                $message = $bool ? Translation :: get('ObjectDeleted', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES) :
                                   Translation :: get('ObjectNotDeleted', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES);
            }
            else
            {
                $objects = Translation :: get('Quotas');
                $message = $bool ? Translation :: get('ObjectsDeleted', array('OBJECTS' => $objects), Utilities :: COMMON_LIBRARIES) :
                                   Translation :: get('ObjectsNotDeleted', array('OBJECTS' => $objects), Utilities :: COMMON_LIBRARIES);
            }

            $this->redirect($message, !$bool, array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTAS));
        }
        else
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NoObjectSelected', null, Utilities :: COMMON_LIBRARIES));
            $this->display_footer();
        }
    }

}
?>