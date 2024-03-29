<?php
namespace application\reservations;

use common\libraries\Translation;
use common\libraries\Display;
use common\libraries\EqualityCondition;
use tracking\Event;
use tracking\ChangesTracker;
use common\libraries\Utilities;
/**
 * $Id: category_deleter.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
/**
 * Component to delete a category
 */
class ReservationsManagerCategoryDeleterComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[ReservationsManager :: PARAM_CATEGORY_ID];

        if (! $this->get_user())
        {
            $this->display_header(null);
            Display :: display_error_message(Translation :: get('NotAllowed', null, Translation :: get('ObjectCreated', array('OBJECT' => $category), Utilities :: COMMON_LIBRARIES)));
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
            $parent = - 1;

            foreach ($ids as $id)
            {
                $categories = $this->retrieve_categories(new EqualityCondition(Category :: PROPERTY_ID, $id));
                $category = $categories->next_result();

                if ($parent == - 1)
                    $parent = $category->get_parent();

                /*$category->set_status(Category :: STATUS_DELETED);

    			$db->clean_display_order($category);

    			$category->set_display_order(0);
    			if(!$category->update()) $bool = false;*/

                if (! $category->delete())
                {
                    $bool = false;
                }
                else
                {
                    Event :: trigger('delete_category', ReservationsManager :: APPLICATION_NAME, array(ChangesTracker :: PROPERTY_REFERENCE_ID => $category->get_id(), ChangesTracker :: PROPERTY_USER_ID => $this->user->get_id()));
                }
            }

            $category = Translation :: get('Category', null, Utilities :: COMMON_LIBRARIES);
            $categories = Translation :: get('Categories', null, Utilities :: COMMON_LIBRARIES);

            if (count($ids) == 1)
            {
                $message = $bool ? Translation :: get('ObjectDeleted', array('OBJECT' => $category), Utilities :: COMMON_LIBRARIES) :
                                   Translation :: get('ObjectNotDeleted', array('OBJECT' => $category), Utilities :: COMMON_LIBRARIES);
            }
            else
            {
                $message = $bool ? Translation :: get('ObjectsDeleted', array('OBJECTS' => $categories), Utilities :: COMMON_LIBRARIES) :
                                   Translation :: get('ObjectsNotDeleted', array('OBJECTS' => $categories), Utilities :: COMMON_LIBRARIES);
            }

            $this->redirect($message, ($bool ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_CATEGORIES, ReservationsManager :: PARAM_CATEGORY_ID => $parent));
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