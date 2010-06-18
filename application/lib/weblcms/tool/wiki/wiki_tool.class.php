<?php
/**
 * $Id: wiki_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.wiki
 */

/**
 * This tool allows a user to publish wikis in his or her course.
 */
class WikiTool extends Tool
{
    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
        	case self :: ACTION_BROWSE:
        		$component = $this->create_component('Browser');
        		break;
        	case self :: ACTION_VIEW:
        		$component = $this->create_component('Viewer');
        		break;
        	case self :: ACTION_PUBLISH:
        		$component = $this->create_component('Publisher');
        		break;
            default :
                $component = $this->create_component('Browser');
        }
        $component->run();
    }

    function get_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_LIST;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_TABLE;
        return $browser_types;
    }
    
    static function get_allowed_types()
    {
        return array(Wiki :: get_type_name());
    }
    
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}
?>