<?php
/**
 * $Id: learning_path_clo_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component
 */

class LearningPathToolCloViewerComponent extends LearningPathTool
{
	private $object;
	
    function run()
    {
        $publication_id = Request :: get(Tool :: PARAM_PUBLICATION_ID);
        $this->publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($publication_id);
        $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $publication_id);
        
        $object_id = Request :: get(LearningPathTool :: PARAM_OBJECT_ID);
        $this->object = RepositoryDataManager :: get_instance()->retrieve_content_object($object_id);
        $this->set_parameter(LearningPathTool :: PARAM_OBJECT_ID, $object_id);
        
        $display = ComplexDisplay :: factory($this, $this->object->get_type());
        $display->run();
    
    }
    
	function display_header()
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
    
	function get_publication()
    {
    	return $this->publication;
    }

}
?>