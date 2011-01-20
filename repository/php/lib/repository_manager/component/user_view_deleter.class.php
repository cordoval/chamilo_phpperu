<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Application;
use common\libraries\Utilities;

/**
 * $Id: user_view_deleter.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerUserViewDeleterComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(RepositoryManager :: PARAM_USER_VIEW);

        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $user_view_id)
            {
                $uv = new UserView();
                $uv->set_id($user_view_id);

                if (! $uv->delete())
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get('ObjectNotDeleted', array('OBJECT' => Translation :: get('UserView')), Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get('ObjectsNotDeleted', array(
                            'OBJECT' => Translation :: get('UserViews')), Utilities :: COMMON_LIBRARIES);
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get('ObjectDeleted', array('OBJECT' => Translation :: get('UserView')), Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get('ObjectsDeleted', array('OBJECT' => Translation :: get('UserViews')), Utilities :: COMMON_LIBRARIES);
                }
            }

            $this->redirect($message, $failures ? true : false, array(
                    Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_USER_VIEWS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected', array(
                    'OBJECT' => Translation :: get('UserView')), Utilities :: COMMON_LIBRARIES)));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_USER_VIEWS)), Translation :: get('RepositoryManagerUserViewBrowserComponent')));
        $breadcrumbtrail->add_help('repository_user_view_deleter');
    }

    function get_additional_parameters()
    {
        return array(RepositoryManager :: PARAM_USER_VIEW);
    }
}
?>