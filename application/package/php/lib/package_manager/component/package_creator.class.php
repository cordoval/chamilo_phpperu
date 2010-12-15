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
require_once WebApplication :: get_application_class_lib_path('package') . 'forms/package_form.class.php';

/**
 * Component to create a new package_language object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerPackageCreatorComponent extends PackageManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
//        $can_add = PackageRights :: is_allowed(PackageRights :: ADD_RIGHT, PackageRights :: LOCATION_LANGUAGES, 'manager');
//        
//        if (! $can_add)
//        {
//            Display :: not_allowed();
//        }
        
        $package = new Package();
        $form = new PackageForm(PackageForm :: TYPE_CREATE, $package, $this->get_url(), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_package();
            $object = Translation :: get('Package');
            $message = $success ? Translation :: get('ObjectCreated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES) : Translation :: get('ObjectNotCreated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES);
            
            $this->redirect($message, ! $success, array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_PACKAGE));
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
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_PACKAGE)), Translation :: get('PackageManagerAdminPackageBrowserComponent')));
    }

}
?>