<?php
/**
 * @package common.html.formvalidator.html_editor.html_editor_file_browser.html_editor_processor
 * @author Hans De Bisschop
 */

abstract class HtmlEditorProcessor
{
    private $selected_content_objects;

    private $parent;

    public static function factory($type, $parent, $selected_content_objects)
    {
        $editor = LocalSetting :: get('html_editor');
        $file = dirname(__FILE__) . '/' . $editor . '/html_editor_' . $editor . '_' . $type . '_processor.class.php';
        $class = 'HtmlEditor' . Utilities :: underscores_to_camelcase($editor) . Utilities :: underscores_to_camelcase($type) . 'Processor';

        if (file_exists($file))
        {
            require_once ($file);
            return new $class($parent, $selected_content_objects);
        }
    }

    function HtmlEditorProcessor($parent, $selected_content_objects)
    {
        $this->set_parent($parent);
        $this->set_selected_content_objects($selected_content_objects);
    }

    function get_selected_content_objects()
    {
        return $this->selected_content_objects;
    }

    function set_selected_content_objects($selected_content_objects)
    {
        $this->selected_content_objects = $selected_content_objects;
    }

    function get_parent()
    {
        return $this->parent;
    }

    function set_parent($parent)
    {
        $this->parent = $parent;
    }

    function get_parameters()
    {
        return $this->get_parent()->get_parameters();
    }

    function get_parameter($key)
    {
        return $this->get_parent()->get_parameter($key);
    }

    function get_repository_document_display_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        $parameters = array_merge(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_DOWNLOAD_DOCUMENT, 'display' => 1), $parameters);

        return Redirect :: get_link(RepositoryManager :: APPLICATION_NAME, $parameters, $filter, $encode_entities, Redirect :: TYPE_CORE);
    }

    function get_repository_document_display_matching_url()
    {
        $matching_url = self :: get_repository_document_display_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID => ''));
        $matching_url = preg_quote($matching_url);

        $original_object_string = '&'. RepositoryManager :: PARAM_CONTENT_OBJECT_ID .'\=';
        $replace_object_string = '&'. RepositoryManager :: PARAM_CONTENT_OBJECT_ID .'\=[0-9]+';

        $matching_url = str_replace($original_object_string, $replace_object_string, $matching_url);

        return '/' . $matching_url . '/';
    }

    abstract function run();
}
?>