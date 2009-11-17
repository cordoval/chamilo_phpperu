<?php
/**
 * $Id: publisher.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * 
 */
class RepositoryManagerPublisherComponent extends RepositoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $wizard = new PublisherWizard($this);
        $wizard->run();
    }
}
?>
