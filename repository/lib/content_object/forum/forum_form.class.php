<?php
/**
 * $Id: forum_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.forum
 */
require_once dirname(__FILE__) . '/forum.class.php';

class ForumForm extends ContentObjectForm
{

    function create_content_object()
    {
        $object = new Forum();
        $object->set_locked($this->exportValue(Forum :: PROPERTY_LOCKED));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_locked($this->exportValue(Forum :: PROPERTY_LOCKED));
        //$this->set_content_object($object);
        return parent :: update_content_object();
    }

    function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('checkbox', 'locked', Translation :: get('ForumLocked'));
        $this->addElement('category');
    }

    function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('checkbox', 'locked', Translation :: get('ForumLocked'));
        $this->addElement('category');
    }

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[ContentObject :: PROPERTY_LOCKED] = $valuearray[3];
        parent :: set_values($defaults);
    }
    
/*function setDefaults($defaults = array())
	{
		$object = $this->get_content_object();
		if($object != null){
			$defaults[Forum :: PROPERTY_LOCKED] = $object->get_locked();
		}
		parent :: setDefaults($defaults);
	}*/

}
?>
