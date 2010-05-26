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
        $content_object_id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        $content_object = $this->retrieve_content_object($content_object_id); dump($content_object);
        
        if($content_object)
        {
        	$complex_builder = ComplexBuilder :: factory($this, $content_object);
        	$complex_builder->run();
        }
        else
        {
        	$this->display_error_page(Translation :: get('NoObjectSelected'));
        }
    }
}
?>