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
class DependencyManagerDeleterComponent extends DependencyManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
//        $can_delete = PackageRights :: is_allowed(PackageRights :: DELETE_RIGHT, PackageRights :: LOCATION_LANGUAGES, 'manager');
//        
//        if (! $can_delete)
//        {
//            Display :: not_allowed();
//        }
        
        $ids = $_GET[self :: PARAM_DEPENDENCY_ID];
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $package_language = PackageDataManager::get_instance()->retrieve_dependency($id);
                
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
            
            $this->redirect($message, ($failures > 0), array(self :: PARAM_DEPENDENCY_ACTION => self :: ACTION_BROWSE));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectsSelected', null, Utilities :: COMMON_LIBRARIES)));
        }
    }

    function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('dependency_deleter');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_DEPENDENCY_ACTION => self :: ACTION_BROWSE)), Translation :: get('DependencyManagerPackageBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_DEPENDENCY_ACTION);
    }
}
?>