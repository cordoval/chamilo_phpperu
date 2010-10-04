<?php
/**
 * $Id: complex_forum_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.forum
 */
require_once Path :: get_library_path() . 'utilities.class.php';

class ComplexForumForm extends ComplexContentObjectItemForm
{
    const TOTAL_PROPERTIES = 0;

    // Inherited
    protected function build_creation_form()
    {
        parent :: build_creation_form();
    }

    // Inherited
    protected function build_editing_form()
    {
        parent :: build_editing_form();
    }

    // Inherited
    function setDefaults($defaults = array ())
    {
        $cloi = $this->get_complex_content_object_item();
        parent :: setDefaults($defaults);
    }

    function get_default_values()
    {
    
    }

    function create_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        return parent :: create_complex_content_object_item();
    }

    function set_csv_values($valuearray)
    {
        parent :: set_values($defaults);
    }

    // Inherited
    function create_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        return parent :: create_complex_content_object_item();
    }

    // Inherited
    function update_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        return parent :: update_complex_content_object_item();
    }
}

?>