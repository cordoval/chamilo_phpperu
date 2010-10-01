<?php
/**
 * $Id: survey_page_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package
 */
class SurveyPageBuilder extends ComplexBuilder //implements ComplexMenuSupport
{
    
    const ACTION_CREATE_SURVEY_PAGE = 'creator';
     const ACTION_CREATE_QUESTION = 'creator';
    const ACTION_BUILD_ROUTING = 'routing';
    
    const PARAM_QUESTION_ID = 'question';
    const PARAM_SURVEY_PAGE_ID = 'survey_page';

    function get_routing_url($selected_complex_content_object_item)
    {
        $complex_content_object_item_id = ($this->get_complex_content_object_item()) ? ($this->get_complex_content_object_item()->get_id()) : null;
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BUILD_ROUTING, self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id, self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item));
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_BUILDER_ACTION;
    }

    function get_creation_links($content_object, $types = array(), $additional_links = array())
    {
        $html[] = '<div class="category_form"><div id="content_object_selection">';
        
        if (count($types) == 0)
        {
            $types = $content_object->get_allowed_types();
        }
        
        foreach ($types as $type)
        {
            $url = $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_CREATE_QUESTION, self :: PARAM_TYPE => 'survey_page', self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
            
            $html[] = '<a href="' . $url . '"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/' . $type . '.png);">';
            $html[] = Translation :: get(ContentObject :: type_to_class($type) . 'TypeName');
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div></a>';
        }
        
        foreach ($this->get_additional_links() as $link)
        {
            $type = $link['type'];
            $html[] = '<a href="' . $link['url'] . '"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/' . $type . '.png);">';
            $html[] = $link['title'];
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div></a>';
        }
        
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/repository.js');
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        
        return implode("\n", $html);
    }

}

?>