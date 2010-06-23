<?php
/**
 * $Id: survey_page_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package
 */
class SurveyPageBuilder extends ComplexBuilder implements ComplexMenuSupport
{

    const ACTION_CREATE_SURVEY_PAGE = 'create';
    const ACTION_BUILD_ROUTING = 'routing';
    const PARAM_QUESTION_ID = 'question';
    const PARAM_SURVEY_PAGE_ID = 'survey_page';

    function run()
    {
        $action = $this->get_action();

        switch ($action)
        {
            case ComplexBuilder :: ACTION_BROWSE :
                $component = $this->create_component('Browser');
                break;
            case ComplexBuilder :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Creator');
                break;
           case ComplexBuilder :: ACTION_MOVE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Mover');
                break;
            case ComplexBuilder :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Deleter');
                break;
            case ComplexBuilder :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Viewer');
                break;
            case ComplexBuilder :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Updater');
                break;
            default :
            	$this->set_action(ComplexBuilder :: ACTION_BROWSE);
            	$component = $this->create_component('Browser');
        }
        $component->run();
    }

    function get_routing_url($selected_complex_content_object_item)
    {
        $complex_content_object_item_id = ($this->get_complex_content_object_item()) ? ($this->get_complex_content_object_item()->get_id()) : null;
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BUILD_ROUTING, self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id, self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item));
    }

	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}

?>