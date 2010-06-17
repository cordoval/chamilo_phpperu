<?php
/**
 * $Id: wiki_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.wiki.component
 */
require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/wiki/display/wiki_display.class.php';

class WikiToolViewerComponent extends WikiTool
{
    private $complex_display;
	private $content_object;
	private $trail;
    
    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $this->trail = $trail = BreadcrumbTrail :: get_instance();
        
        $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, Request :: get(Tool :: PARAM_PUBLICATION_ID));
        $this->complex_display = ComplexDisplay :: factory($this, Wiki :: get_type_name());
        
        $object = WebLcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID));
        $object = $object->get_content_object();
        
        $this->content_object = $object;
        $this->complex_display->run();
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
        return $this->content_object;
    }
}
?>