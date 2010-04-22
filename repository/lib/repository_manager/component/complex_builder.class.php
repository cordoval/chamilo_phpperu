<?php
/**
 * $Id: complex_builder.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */

/**
 * Component to build complex learning object items
 * @author vanpouckesven
 *
 */
class RepositoryManagerComplexBuilderComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $complex_builder = ComplexBuilder :: factory($this);
        $complex_builder->run();
    }

    function display_header($breadcrumbtrail, $helpitem)
    {
        parent :: display_header($breadcrumbtrail, false, false, $helpitem);
    }
}
?>