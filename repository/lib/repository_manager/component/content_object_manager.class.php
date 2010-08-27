<?php
/**
 * @package repository.lib.repository_manager.component
 *
 * @author Eduard Vossen
 * @author Hans De Bisschop
 */
class RepositoryManagerContentObjectManagerComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $object_type = Request :: get(self :: PARAM_CONTENT_OBJECT_TYPE);
        $manage_type = Request :: get(self :: PARAM_CONTENT_OBJECT_MANAGER_TYPE);
        
        $this->set_parameter(self :: PARAM_CONTENT_OBJECT_TYPE, $object_type);
        $this->set_parameter(self :: PARAM_CONTENT_OBJECT_MANAGER_TYPE, $manage_type);
        
        if (isset($object_type) && isset($manage_type))
        {
            require_once Path :: get_repository_path() . 'lib/content_object/' . $object_type . '/manage/' . $manage_type . '/' . $object_type . '_' . $manage_type . '_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($object_type . '_' . $manage_type) . 'Manager';
            
            call_user_func(array($class, 'launch'), $this);
        }
        else
        {
            $this->redirect(null, false, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS));
            Display :: header(BreadcrumbTrail :: get_instance());
            Display :: warning_message(Translation :: get('NoSuchContentObjectManagerExists'));
            Display :: footer();
        }
    }
}
?>