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

	private $content_object;
	
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $content_object_id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        $this->set_parameter(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, $content_object_id);
        $this->content_object = $this->retrieve_content_object($content_object_id);
        
//        $type = Request :: get(RepositoryManager :: PARAM_TYPE);
//     	$this->set_parameter(RepositoryManager :: PARAM_TYPE, $type);
//
//        if($type)
//        {
//            ComplexBuilder :: launch($type, $this);
//                  	//$complex_builder = ComplexBuilder :: factory($this, $this->content_object->get_type());
//        	//$complex_builder->run();
//        }
        
        if($this->content_object)
        {
            ComplexBuilder :: launch($this->content_object->get_type(), $this);
        	//$complex_builder = ComplexBuilder :: factory($this, $this->content_object->get_type());
        	//$complex_builder->run();
        }
        else
        {
        	$this->display_error_page(Translation :: get('NoObjectSelected'));
        }
    }
    
    function get_root_content_object()
    {
    	return $this->content_object;
    }
    
    function redirect_away_from_complex_builder($message, $error_message)
    {
    	$this->redirect($message, $error_message, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS));
    }
}
?>