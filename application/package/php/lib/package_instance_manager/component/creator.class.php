<?php

namespace application\package;

use common\libraries\WebApplication;
use common\libraries\Display;
use common\libraries\Translation;
use common\libraries\AdministrationComponent;
use common\libraries\Breadcrumb;
use common\libraries\Utilities;

/**
 * @package application.package.package.component
 */

/**
 * Component to create a new package_language object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageInstanceManagerCreatorComponent extends PackageInstanceManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $package = new Package();
        $form = new PackageForm(PackageForm :: TYPE_CREATE, $package, $this->get_url(), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_package();
            if ($success)
            {
               PackageDataManager::generate_packages_xml();
            }
            $object = Translation :: get('Package');
            $message = $success ? Translation :: get('ObjectCreated', array(
                    'OBJECT' => $object), Utilities :: COMMON_LIBRARIES) : Translation :: get('ObjectNotCreated', array(
                    'OBJECT' => $object), Utilities :: COMMON_LIBRARIES);
            
            $this->redirect($message, ! $success, array(
                    PackageInstanceManager :: PARAM_PACKAGE_INSTANCE_ACTION => PackageInstanceManager :: ACTION_BROWSE));
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
        $breadcrumbtrail->add_help('package_creator');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                PackageInstanceManager :: PARAM_PACKAGE_INSTANCE_ACTION => PackageInstanceManager :: ACTION_BROWSE)), Translation :: get('PackageManagerBrowserComponent')));
    }

}
?>