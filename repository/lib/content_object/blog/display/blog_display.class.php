<?php
/**
 * $Id: blog_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.blog
 */

/**
 * This tool allows a user to publish blogs in his or her course.
 */
class BlogDisplay extends ComplexDisplay
{
    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();
            
        switch ($action)
        {
            case self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Creator');
                break;
            case self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Deleter');
                break;
            case self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Updater');
                break;
            default :
             	$this->set_action(self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT);
                $component = $this->create_component('Viewer');
        }
        $component->run();
    }

    function get_application_component_path()
    {
		return dirname(__FILE__) . '/component/';
    }
}
?>