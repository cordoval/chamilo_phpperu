<?php
/**
 * $Id: wiki_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.wiki.component
 */
require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/wiki/wiki_display.class.php';

class WikiToolViewerComponent extends WikiToolComponent
{
    private $cd;
	private $trail;
    
    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $this->trail = $trail = new BreadcrumbTrail();
        
        $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, Request :: get(Tool :: PARAM_PUBLICATION_ID));
        $this->cd = ComplexDisplay :: factory($this, Wiki :: get_type_name());
        
        $o = WebLcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID));
        $o = $o->get_content_object();
        
        $this->cd->set_root_lo($o);
        $this->cd->run();
    }
    
	function display_header($trail)
    {
    	if($trail)
    	{
    		$this->trail->merge($trail);
    	}
    	
    	return parent :: display_header($this->trail);
    }

}
?>