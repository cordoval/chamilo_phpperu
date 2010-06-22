<?php
/**
 * $Id: content_object_include_parser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
abstract class ContentObjectIncludeParser
{
    /**
     * The form
     */
    private $form;

    public function __construct($form)
    {
        $this->form = $form;
    }

    function get_form()
    {
        return $this->form;
    }

    function set_form($form)
    {
        $this->form = $form;
    }

    abstract function parse_editor();

    static function factory($type, $form)
    {
        $class = 'Include' . Utilities :: underscores_to_camelcase($type) . 'Parser';
        require_once dirname(__FILE__) . '/includes/include_' . $type . '_parser.class.php';
        return new $class($form);
    }

    static function get_include_types()
    {
        return array('image', Wiki :: get_type_name(), 'embed', Youtube :: get_type_name());
    }

    function parse_includes($form)
    {
        $content_object = $form->get_content_object();

        $form_type = $form->get_form_type();

        if ($form_type == ContentObjectForm :: TYPE_EDIT)
        {
            /*
				 * TODO: Make this faster by providing a function that matches the
				 *      existing IDs against the ones that need to be added, and
				 *      attaches and detaches accordingly.
				 */
            foreach ($content_object->get_included_content_objects() as $included_object)
            {
                $content_object->exclude_content_object($included_object->get_id());
            }
        }

        $include_types = self :: get_include_types();
        foreach ($include_types as $include_type)
        {
            $parser = self :: factory($include_type, $form);
            $parser->parse_editor();
        }
    }

    function get_base_path()
    {
        return Path :: get(REL_REPO_PATH);
    }
}
?>