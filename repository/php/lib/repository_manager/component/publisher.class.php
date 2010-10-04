<?php
/**
 * $Id: publisher.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * 
 */
class RepositoryManagerPublisherComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $wizard = new PublisherWizard($this);
        $wizard->run();
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('repository_publisher');
    }
}
?>