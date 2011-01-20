<?php
namespace application\package;

use common\libraries\Display;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\Utilities;
/**
 * Component to create a new package_language object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class DependencyManagerCreatorComponent extends DependencyManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $package = new Dependency();
        $form = new DependencyForm(DependencyForm :: TYPE_CREATE, $package, $this->get_url(), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_dependency();
            $object = Translation :: get('Dependency');
            $message = $success ? Translation :: get('ObjectCreated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES) : Translation :: get('ObjectNotCreated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES);
            
            $this->redirect($message, ! $success, array(DependencyManager :: PARAM_DEPENDENCY_ACTION => DependencyManager :: ACTION_BROWSE));
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
        $breadcrumbtrail->add_help('dependency_creator');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(DependencyManager :: PARAM_DEPENDENCY_ACTION => DependencyManager :: ACTION_BROWSE)), Translation :: get('DependencyManagerBrowserComponent')));
    }
}
?>