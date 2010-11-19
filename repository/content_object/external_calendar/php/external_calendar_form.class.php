<?php
namespace repository\content_object\external_calendar;

use common\libraries\Translation;

use repository\ContentObjectForm;

/**
 * $Id: external_calendar_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.link
 */
require_once dirname(__FILE__) . '/external_calendar.class.php';
class ExternalCalendarForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));
        $this->add_textfield(ExternalCalendar :: PROPERTY_URL, Translation :: get('ICalURL'), true, array('size' => '100'));
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get('Properties'));
        $this->add_textfield(ExternalCalendar :: PROPERTY_URL, Translation :: get('ICalURL'), true, array('size' => '100'));
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[ExternalCalendar :: PROPERTY_URL] = $lo->get_url();
        }
        else
        {
            $defaults[ExternalCalendar :: PROPERTY_URL] = 'http://';
        }
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $object = new ExternalCalendar();
        $object->set_url($this->exportValue(ExternalCalendar :: PROPERTY_URL));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_url($this->exportValue(ExternalCalendar :: PROPERTY_URL));
        return parent :: update_content_object();
    }
}
?>