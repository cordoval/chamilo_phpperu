<?php
/**
 * $Id: forum_topic_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum_topic
 */

class ForumTopicBuilder extends ComplexBuilder
{

    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case ComplexBuilder :: ACTION_BROWSE :
                $component = $this->create_component('Browser');
                break;
            case ComplexBuilder :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Deleter');
                break;
            default :
                $component = $this->create_component('Browser');
                break;
        }
        
        $component->run();
    }
 
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}

?>