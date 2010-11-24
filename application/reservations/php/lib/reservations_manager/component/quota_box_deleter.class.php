<?php
namespace application\reservations;

use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Utilities;

use tracking\Event;
use tracking\ChangesTracker;
/**
 * $Id: quota_box_deleter.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
/**
 * Component to delete a category
 */
class ReservationsManagerQuotaBoxDeleterComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(ReservationsManager :: PARAM_QUOTA_BOX_ID);

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
                $box = new QuotaBox();
                $box->set_id($id);

                if (! $box->delete())
                {
                    $bool = false;
                }
                else
                {
                    Event :: trigger('delete_quota_box', ReservationsManager :: APPLICATION_NAME, array(ChangesTracker :: PROPERTY_REFERENCE_ID => $id, ChangesTracker :: PROPERTY_USER_ID => $this->get_user_id()));
                }

            }

            if (count($ids) == 1)
            {
                $object = Translation :: get('QuotaBox');
                $message = $bool ? Translation :: get('ObjectDeleted', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES) :
                                   Translation :: get('ObjectNotDeleted', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES);
            }
            else
            {
                $objects = Translation :: get('QuotaBoxes');
                $message = $bool ? Translation :: get('ObjectsDeleted', array('OBJECTS' => $objects), Utilities :: COMMON_LIBRARIES) :
                                   Translation :: get('ObjectsNotDeleted', array('OBJECTS' => $objects), Utilities :: COMMON_LIBRARIES);
            }

            $this->redirect($message, !$bool, array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTA_BOXES));
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