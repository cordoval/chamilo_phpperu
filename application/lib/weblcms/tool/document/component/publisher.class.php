<?php
/**
 * $Id: document_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.document.component
 */

class DocumentToolPublisherComponent extends DocumentTool
{
    function run()
    {
    	//TODO: change this to real roles and rights
    	$category = $this->get_category(Request :: get(WeblcmsManager :: PARAM_CATEGORY));
        if($category && $category->get_name() == 'Dropbox')
        {
        	$this->get_parent()->set_right(ADD_RIGHT, true);
        }
        
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_PUBLISH, $this);
        $component->run();
    }
}
?>