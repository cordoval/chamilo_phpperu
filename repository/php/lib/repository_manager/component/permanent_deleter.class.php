<?php
/**
 * Repository manager component which provides functionality to permanently
 * delete a content object from the users repository.
 *
 * @package repository.lib.repository_manager.component
 * @author Hans De Bisschop
 */

require_once dirname(__FILE__) . '/deleter.class.php';

class RepositoryManagerPermanentDeleterComponent extends RepositoryManagerDeleterComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Request :: set_get(self :: PARAM_DELETE_PERMANENTLY, 1);
        parent :: run();
    }
}
?>