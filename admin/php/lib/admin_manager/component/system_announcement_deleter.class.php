<?php
namespace admin;

use common\libraries\Breadcrumb;
use common\libraries\Utilities;
use common\libraries\Application;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\AdministrationComponent;

/**
 * $Id: system_announcement_deleter.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */

class AdminManagerSystemAnnouncementDeleterComponent extends AdminManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(AdminManager :: PARAM_SYSTEM_ANNOUNCEMENT_ID);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $publication = $this->retrieve_system_announcement_publication($id);

                if (! $publication->delete())
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'ContentObjectNotDeleted';
                    $parameter = array('OBJECT' => Translation :: get('Publication'));
                }
                else
                {
                    $message = 'ContentObjectsNotDeleted';
                    $parameter = array('OBJECTS' => Translation :: get('Publications'));
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ContentObjectDeleted';
                    $parameter = array('OBJECT' => Translation :: get('Publication'));
                }
                else
                {
                    $message = 'ContentObjectsDeleted';
                    $parameter = array('OBJECTS' => Translation :: get('Publications'));
                }
            }

            $this->redirect(Translation :: get($message, $parameter, Utilities :: COMMON_LIBRARIES), ($failures ? true : false), array(
                    Application :: PARAM_ACTION => AdminManager :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected', array(
                    'OBJECTS' => Translation :: get('Publication')), Utilities :: COMMON_LIBRARIES)));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                AdminManager :: PARAM_ACTION => AdminManager :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS)), Translation :: get('AdminManagerSystemAnnouncementBrowserComponent')));
        $breadcrumbtrail->add_help('admin_system_announcements_deleter');
    }

    function get_additional_parameters()
    {
        return array(AdminManager :: PARAM_SYSTEM_ANNOUNCEMENT_ID);
    }
}
?>