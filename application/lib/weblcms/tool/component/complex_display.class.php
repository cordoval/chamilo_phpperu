<?php
/**
 * $Id: complex_builder.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
class ToolComplexDisplayComponent extends ToolComponent
{
	private $object;
	
    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $publication_id = Request :: get(Tool :: PARAM_PUBLICATION_ID);
        $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $publication_id);
         
		$publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($publication_id);
		$this->object = $publication->get_content_object();
	
        $display = ComplexDisplay :: factory($this, $this->object->get_type());
        $display->run();
    }
    
    function get_root_content_object()
    {
    	return $this->object;
    }
}
?>