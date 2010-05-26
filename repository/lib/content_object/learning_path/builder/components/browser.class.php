<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path.component
 */
//require_once dirname(__FILE__) . '/../learning_path_builder_component.class.php';
require_once dirname(__FILE__) . '/browser/learning_path_browser_table_cell_renderer.class.php';

class LearningPathBuilderBrowserComponent extends LearningPathBuilder
{

    function run()
    {
        $browser = ComplexBuilderComponent::factory(ComplexBuilderBrowserComponent :: BROWSER_COMPONENT, $this);
    
        $browser->run();
    }

    function get_object_info()
    {
        $html = array();

        $content_object = $this->get_root_content_object();
        $display = ContentObjectDisplay :: factory($content_object);
        $content_object_display = $display->get_full_html();
        $check_empty = trim(strip_tags($content_object_display));

        if (! empty($check_empty) && $check_empty != $content_object->get_title())
        {
            $html[] = '<div class="complex_browser_display">';
            $html[] = $content_object_display;
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div>';
        }

        return implode("\n", $html);
    }

    function get_action_bar()
    {
        $pub = Request :: get('publish');

        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if ($pub && $pub != '')
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $_SESSION['redirect_url']));
            return $action_bar;
        }
    }

    function get_creation_links($content_object, $types = array())
    {
        $html[] = '<div class="select_complex_element">';
        $html[] = '<span class="title">' . Theme :: get_common_image('place_content_objects') . Translation :: get('LearningPathAddContentObject') . '</span>';
        $html[] = '<div id="content_object_selection">';

        if (count($types) == 0)
        {
            $types = $content_object->get_allowed_types();
        }

        foreach ($types as $type)
        {
            if ($type == LearningPath :: get_type_name())
            {
                $url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_ROOT_CONTENT_OBJECT => $this->get_root_content_object()->get_id(), ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID_ID => ($this->get_complex_content_object_item() ? $this->get_complex_content_object_item()->get_id() : null), 'publish' => Request :: get('publish')));
            }
            else
            {
                $url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => LearningPathBuilder :: ACTION_CREATE_LP_ITEM, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_ROOT_CONTENT_OBJECT => $this->get_root_content_object()->get_id(), ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => ($this->get_complex_content_object_item() ? $this->get_complex_content_object_item()->get_id() : null), 'publish' => Request :: get('publish')));
            }

            $html[] = '<a href="' . $url . '"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/' . $type . '.png);">';
            $html[] = Translation :: get(ContentObject :: type_to_class($type) . 'TypeName');
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div></a>';
        }

        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/repository.js');
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }
}

?>