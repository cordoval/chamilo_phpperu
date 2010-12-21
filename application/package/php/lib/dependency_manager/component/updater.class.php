<?php

namespace application\package;

use common\libraries\WebApplication;
use common\libraries\Display;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\AdministrationComponent;
use common\libraries\Request;
use common\libraries\Utilities;

/**
 * @package application.package.package.component
 */

/**
 * Component to edit an existing package_language object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class DependencyManagerUpdaterComponent extends DependencyManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
//        $can_edit = PackageRights :: is_allowed(PackageRights :: EDIT_RIGHT, PackageRights :: LOCATION_LANGUAGES, 'manager');
//        
//        if (! $can_edit)
//        {
//            Display :: not_allowed();
//        }
        
        $package = PackageDataManager::get_instance()->retrieve_dependency(Request :: get(DependencyManager :: PARAM_DEPENDENCY_ID));
        $form = new DependencyForm(DependencyForm :: TYPE_EDIT, $package, $this->get_url(array(DependencyManager :: PARAM_DEPENDENCY_ID => $package->get_id())), $this->get_user());
        if ($form->validate())
        {
            $success = $form->update_dependency();
            $object = Translation :: get('Dependency');
            $message = $success ? Translation :: get('ObjectUpdated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES) : Translation :: get('ObjectNotUpdated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES);
            $this->redirect($message, ! $success, array(PackageInstanceManager :: PARAM_PACKAGE_INSTANCE_ACTION => DependencyManager :: ACTION_BROWSE));
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('author_updater');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageInstanceManager :: PARAM_PACKAGE_INSTANCE_ACTION => DependencyManager :: ACTION_BROWSE)), Translation :: get('DependencyManagerDependencyBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_DEPENDENCY_ID);
    }
}
?>