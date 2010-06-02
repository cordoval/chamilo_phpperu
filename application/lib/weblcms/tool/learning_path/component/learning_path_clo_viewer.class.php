<?php
/**
 * $Id: learning_path_clo_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component
 */

class LearningPathToolCloViewerComponent extends LearningPathToolComponent
{

	private $object;
	
    function run()
    {
        $object_id = Request :: get(Tool :: PARAM_PUBLICATION_ID);
        $object = RepositoryDataManager :: get_instance()->retrieve_content_object($object_id);
        $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $object_id);
        $display = ComplexDisplay :: factory($this, $object->get_type());
        //$display->set_root_content_object($object);
        $this->object=$object;
        $display->run();
    
    }
    
	function display_header($trail)
    {
    	return Display :: small_header();
    }
    
    function display_footer()
    {
    	return null;
    }
    
	function get_root_content_object()
    {
    	return $this->object;
    }

}
?>