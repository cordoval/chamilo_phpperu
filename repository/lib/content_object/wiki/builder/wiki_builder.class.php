<?php
/**
 * $Id: wiki_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.wiki
 */

class WikiBuilder extends ComplexBuilder
{
    const ACTION_SELECT_HOMEPAGE = 'select_home';

    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case ComplexBuilder :: ACTION_BROWSE :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_SELECT_HOMEPAGE :
                $component = $this->create_component('HomepageSelector');
                break;
            default :
                $component = $this->create_component('Browser');
                break;
        }
        $component->run();
    }

    function get_select_homepage_url($complex_content_object_item)
    {
        return $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => self :: ACTION_SELECT_HOMEPAGE, ComplexBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id()));
    }

	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}

?>