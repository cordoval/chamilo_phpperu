<?php
/**
 * $Id: search_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.search
 */

/**
 * This tool allows a user to publish course_settingss in his or her course.
 */
class SearchTool extends Tool
{
    const ACTION_SEARCH = 'search';

    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case self :: ACTION_SEARCH :
                $component = $this->create_component('Searcher');
                break;
            default :
                $component = $this->create_component('Searcher');
        }
        
        $component->run();
    }
    
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}
?>