<?php
/**
 * $Id: glossary_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.glossary.component
 */


/**
 * Represents the view component for the assessment tool.
 *
 */
class GlossaryToolViewerComponent extends GlossaryTool
{
	private $trail;
	private $object;
	
    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $publication_id = Request :: get(Tool :: PARAM_PUBLICATION_ID);
		$publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($publication_id);
		$this->object = $publication->get_content_object();
	
        $this->trail = $trail = BreadcrumbTrail :: get_instance();
        
        $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $publication_id);
        
        $display = ComplexDisplay :: factory($this, $this->object->get_type());
        $display->run();
    }
    
	function display_header($trail)
    {
    	if($trail)
    	{
    		$this->trail->merge($trail);
    	}
    	
    	return parent :: display_header($this->trail);
    }
    
    function get_root_content_object()
    {
    	return $this->object;
    }
}

?>