<?php
/**
 * $Id: competence_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.competence
 */
//require_once dirname(__FILE__) . '/competence_builder_component.class.php';

class CompetenceBuilder extends ComplexBuilder implements ComplexMenuSupport
{

function run()
    {
        $action = $this->get_action();
        switch ($action)
        {
            case ComplexBuilder :: ACTION_BROWSE:
                $component = $this->create_component('Browser');//IndicatorBuilderComponent :: factory('Browser', $this);
                break;
            case ComplexBuilder :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM :
            	$component = $this->create_component('Deleter');
                break;
           case ComplexBuilder :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM :
            	$component = $this->create_component('Creator');
                break;
            case ComplexBuilder :: ACTION_MOVE_COMPLEX_CONTENT_OBJECT_ITEM :
            	$component = $this->create_component('Mover');
                break;
            case ComplexBuilder :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM :
            	$component = $this->create_component('Updater');
                break;
            case ComplexBuilder :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT_ITEM :
            	$component = $this->create_component('Viewer');
                break;
            case ComplexBuilder :: ACTION_CHANGE_PARENT :
            	$component = $this->create_component('ParentChanger');
                break;
			default:
            	$this->set_action(ComplexBuilder :: ACTION_BROWSE);
                $component = $this->create_component('Browser');
                break;
        }

//        if (! $component)
//            parent :: run();
//        else
        $component->run();
    }
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}

?>