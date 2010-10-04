<?php
/**
 * Repository manager component which provides functionality to create a
 * template based on another content object
 *
 * @package repository.lib.repository_manager.component
 * @author Hans De Bisschop
 */

require_once dirname(__FILE__) . '/content_object_copier.class.php';

class RepositoryManagerTemplateCreatorComponent extends RepositoryManagerContentObjectCopierComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Request :: set_get(self :: PARAM_TARGET_USER, 0);
        parent :: run();
    }
}
?>