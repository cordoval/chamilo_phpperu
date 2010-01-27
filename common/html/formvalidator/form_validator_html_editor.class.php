<?php

class FormValidatorHtmlEditor
{
    private $form;
    private $name;
    private $label;
    private $required;
    private $attributes;

    function __construct($form, $name, $label, $required = true, $attributes = array())
    {
        $this->form = $form;
        $this->name = $name;
        $this->label = $label;
        $this->required = $required;

        if (! array_key_exists('size', $attributes))
        {
            $attributes['size'] = 50;
        }

        if (! array_key_exists('style', $attributes))
        {
            $attributes['style'] = 'width: 500px; height: 75px;';
        }

        $attributes['class'] = 'html_editor';

//        if (array_key_exists('toolbar', $attributes))
//        {
//            $attributes['class'] = $attributes['class'] . ' ' . $attributes['toolbar'];
//        }

        $this->attributes = $attributes;
    }

    function add()
    {
        $form = $this->get_form();
        $element = $this->create();

        $form->addElement($element);
        $form->applyFilter($this->get_name(), 'trim');

        if ($this->get_required())
        {
            $form->addRule($this->get_name(), Translation :: get('ThisFieldIsRequired'), 'required');
        }
    }

    function create()
    {
        return $this->get_form()->createElement('textarea', $this->name, $this->label, $this->attributes);
//        return $this->get_form()->createElement('textarea', $this->name, $this->label, $this->attributes);
    }

    function get_form()
    {
        return $this->form;
    }

    function set_form($form)
    {
        $this->form = $form;
    }

    function get_name()
    {
        return $this->name;
    }

    function set_name($name)
    {
        $this->name = $name;
    }

    function get_label()
    {
        return $this->label;
    }

    function set_label($label)
    {
        $this->label = $label;
    }

    function get_attributes()
    {
        return $this->attributes;
    }

    function set_attributes($attributes)
    {
        $this->attributes = $attributes;
    }

    function get_required()
    {
        return $this->required;
    }

    function set_required($required)
    {
        $this->required = $required;
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