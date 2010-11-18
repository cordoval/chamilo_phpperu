<?php
namespace repository\content_object\wiki;

use common\libraries\Request;
use common\libraries\Translation;

use repository\ContentObject;
use repository\ContentObjectForm;

/**
 * $Id: wiki_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.wiki
 */
require_once dirname(__FILE__) . '/wiki.class.php';

class WikiForm extends ContentObjectForm
{

    function create_content_object()
    {
        $object = new Wiki();
        $object->set_locked($this->exportValue(Wiki :: PROPERTY_LOCKED));
        $object->set_links($this->exportValue(Wiki :: PROPERTY_LINKS));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_locked($this->exportValue(Wiki :: PROPERTY_LOCKED));
        $object->set_links($this->exportValue(Wiki :: PROPERTY_LINKS));
        $this->set_content_object($object);
        return parent :: update_content_object();
    }

    function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));
        $this->addElement('checkbox', 'locked', Translation :: get('WikiLocked'));
        $this->add_html_editor('links', Translation :: get('WikiToolBoxLinks'), false);
        //$this->addElement('textarea', 'links', Translation :: get('WikiToolBoxLinks'), array('rows' => 5, 'cols' => 100));
        $this->addElement('category');
    }

    function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get('Properties'));
        $this->addElement('checkbox', 'locked', Translation :: get('WikiLocked'));
        $this->add_html_editor('links', Translation :: get('WikiToolBoxLinks'), false);
        //$this->addElement('textarea', 'links', Translation :: get('WikiToolBoxLinks'), array('rows' => 5, 'cols' => 100));
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {

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