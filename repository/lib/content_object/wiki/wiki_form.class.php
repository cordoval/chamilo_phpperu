<?php
/**
 * $Id: wiki_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.wiki
 */
require_once dirname(__FILE__) . '/wiki.class.php';
require_once dirname(__FILE__) . '/display/wiki_parser.class.php';

class WikiForm extends ContentObjectForm
{

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[Wiki :: PROPERTY_LOCKED] = $valuearray[3];
        $defaults[Wiki :: PROPERTY_LINKS] = $valuearray[4];
        parent :: set_values($defaults);
    }

    function create_content_object()
    {
        $object = new Wiki();
        $parser = new WikiParser($this, Request :: get('pid'), Request :: get('course'));
        $object->set_locked($this->exportValue(Wiki :: PROPERTY_LOCKED));
        $object->set_links($this->exportValue(Wiki :: PROPERTY_LINKS));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $parser = new WikiParser($this, Request :: get('pid'), Request :: get('course'));
        $object = $this->get_content_object();
        $object->set_locked($this->exportValue(Wiki :: PROPERTY_LOCKED));
        $object->set_links($this->exportValue(Wiki :: PROPERTY_LINKS));
        $this->set_content_object($object);
        return parent :: update_content_object();
    }

    function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('checkbox', 'locked', Translation :: get('WikiLocked'));
        $this->addElement('textarea', 'links', Translation :: get('WikiToolBoxLinks'), array('rows' => 5, 'cols' => 100));
        $this->addElement('category');
    }

    function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('checkbox', 'locked', Translation :: get('WikiLocked'));
        $this->addElement('textarea', 'links', Translation :: get('WikiToolBoxLinks'), array('rows' => 5, 'cols' => 100));
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $parser = new WikiParser();
        
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[ContentObject :: PROPERTY_ID] = $lo->get_id();
            
            $defaults[ContentObject :: PROPERTY_TITLE] = $lo->get_title();
            $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $lo->get_description();
            $defaults[Wiki :: PROPERTY_LOCKED] = $lo->get_locked();
            $defaults[Wiki :: PROPERTY_LINKS] = $lo->get_links();
        }
        
        parent :: setDefaults($defaults);
    }

}
?>