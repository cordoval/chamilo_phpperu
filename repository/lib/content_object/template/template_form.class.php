<?php
/**
 * $Id: template_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.template
 */
require_once dirname(__FILE__) . '/template.class.php';
/**
 * This class represents a form to create or update templates
 */
class TemplateForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->build_default_form();
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->build_default_form();
        $this->addElement('category');
    }

    private function build_default_form()
    {
        $this->add_html_editor(Template :: PROPERTY_DESIGN, Translation :: get('Design'), false);

    }

    function setDefaults($defaults = array ())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[Template :: PROPERTY_DESIGN] = $lo->get_design();
        }

        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $values = $this->exportValues();

        $object = new Template();
        $object->set_design($values[Template :: PROPERTY_DESIGN]);
        parent :: set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $values = $this->exportValues();

        $object = $this->get_content_object();
        $object->set_design($values[Template :: PROPERTY_DESIGN]);
        parent :: set_content_object($object);
        return parent :: update_content_object();
    }
}
?>