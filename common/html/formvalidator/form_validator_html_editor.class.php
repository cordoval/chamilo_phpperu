<?php

class FormValidatorHtmlEditor
{
    private $form;

    function __construct($form, $name, $label, $required = true, $attributes = array())
    {
        $this->form = $form;

        if (! array_key_exists('size', $attributes))
        {
            $attributes['size'] = 50;
        }

        $attributes['style'] = 'width: 500px; height: 75px;';
        $attributes['class'] = 'html_editor';

        $element = $form->addElement('textarea', $name, $label, $attributes);
        $form->applyFilter($name, 'trim');

        if ($required)
        {
            $form->addRule($name, Translation :: get('ThisFieldIsRequired'), 'required');
        }
    }

    function get_form()
    {
        return $this->form;
    }

    function set_form($form)
    {
        $this->form = $form;
    }

    public static function factory($type, $form, $name, $label, $required = true, $attributes = array())
    {
        $file = dirname(__FILE__) . '/html_editor/' . $type . '_html_editor.class.php';
        $class = 'FormValidator' . Utilities :: underscores_to_camelcase($type) . 'HtmlEditor';

        if (file_exists($file))
        {
            require_once ($file);
            return new $class($form, $name, $label, $required, $attributes);
        }
    }
}
?>