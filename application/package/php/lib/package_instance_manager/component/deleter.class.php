<?php
namespace application\package;

use common\libraries\WebApplication;
use common\libraries\Display;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\AdministrationComponent;
use common\libraries\Utilities;

/**
 * @package application.package.package.component
 */

/**
 * Component to delete package_languages objects
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageInstanceManagerDeleterComponent extends PackageInstanceManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $can_delete = PackageRights :: is_allowed(PackageRights :: DELETE_RIGHT, PackageRights :: LOCATION_LANGUAGES, 'manager');
        
        if (! $can_delete)
        {
            Display :: not_allowed();
        }
        
        $ids = $_GET[PackageManager :: PARAM_PACKAGE];
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $package_language = $this->retrieve_package($id);
                
                if (! $package_language->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get('ObjectNotDeleted', array('OBJECT' => Translation :: get('Package')), Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get('ObjectsNotDeleted', array('OBJECTS' => Translation :: get('Package')), Utilities :: COMMON_LIBRARIES);
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get('ObjectDeleted', array('OBJECT' => Translation :: get('Packages')), Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get('ObjectsDeleted', array('OBJECTS' => Translation :: get('Packages')), Utilities :: COMMON_LIBRARIES);
                }
            }
            
            $this->redirect($message, ! $failures, array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_PACKAGE));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectsSelected', null, Utilities :: COMMON_LIBRARIES)));
        }
    }

    function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('package_deleter');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_PACKAGE)), Translation :: get('PackageManagerAdminPackageBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PACKAGE);
    }
}
?>