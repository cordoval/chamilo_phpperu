<?php
/**
 * $Id: calendar_event_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.calendar_event
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__) . '/calendar_event.class.php';
/**
 * This class represents a form to create or update calendar events
 */
class CalendarEventForm extends ContentObjectForm
{
    const TOTAL_PROPERTIES = 4;
    const PARAM_REPEAT = 'repeated';
    const PARAM_REPEAT_DATE = 'repeate_date';

    // Inherited
    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_timewindow(CalendarEvent :: PROPERTY_START_DATE, CalendarEvent :: PROPERTY_END_DATE, Translation :: get('StartTimeWindow'), Translation :: get('EndTimeWindow'));

        $choices[] = $this->createElement('radio', self :: PARAM_REPEAT, '', Translation :: get('No'), 0, array('onclick' => 'javascript:timewindow_hide(\'repeat_timewindow\')', 'id' => self :: PARAM_REPEAT));
        $choices[] = $this->createElement('radio', self :: PARAM_REPEAT, '', Translation :: get('Yes'), 1, array('onclick' => 'javascript:timewindow_show(\'repeat_timewindow\')'));
        $this->addGroup($choices, null, Translation :: get('Repeat'), '<br />', false);
        $this->addElement('html', '<div style="padding-left: 25px; display: block;" id="repeat_timewindow">');

        $options = CalendarEvent :: get_repeat_options();

        $repeat_elements = array();
        $repeat_elements[] = $this->createElement('select', CalendarEvent :: PROPERTY_REPEAT_TYPE, null, $options);
        $repeat_elements[] = $this->createElement('static', null, null, Translation :: get('Until'));
        $repeat_elements[] = $this->createElement('datepicker', CalendarEvent :: PROPERTY_REPEAT_TO, '', array('form_name' => $this->getAttribute('name'), 'class' => CalendarEvent :: PROPERTY_REPEAT_TO), true);
        $this->addGroup($repeat_elements, self :: PARAM_REPEAT_DATE, null, '&nbsp;', false);
        $this->addGroupRule(self :: PARAM_REPEAT_DATE, array(CalendarEvent :: PROPERTY_REPEAT_TO => array(array(Translation :: get('InvalidDate'), 'date'))));

        $this->addElement('html', '</div>');
        $this->addElement('html', "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var expiration = document.getElementById('" . self :: PARAM_REPEAT . "');
					if (expiration.checked)
					{
						timewindow_hide('repeat_timewindow');
					}
					function timewindow_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function timewindow_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");
        $this->addElement('category');
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/dates.js'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/doubletimepicker.js'));
    }

    // Inherited
    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_timewindow(CalendarEvent :: PROPERTY_START_DATE, CalendarEvent :: PROPERTY_END_DATE, Translation :: get('StartTimeWindow'), Translation :: get('EndTimeWindow'));

        $choices[] = $this->createElement('radio', self :: PARAM_REPEAT, '', Translation :: get('No'), 0, array('onclick' => 'javascript:timewindow_hide(\'repeat_timewindow\')', 'id' => self :: PARAM_REPEAT));
        $choices[] = $this->createElement('radio', self :: PARAM_REPEAT, '', Translation :: get('Yes'), 1, array('onclick' => 'javascript:timewindow_show(\'repeat_timewindow\')'));
        $this->addGroup($choices, null, Translation :: get('Repeat'), '<br />', false);
        $this->addElement('html', '<div style="padding-left: 25px; display: block;" id="repeat_timewindow">');

        $options = CalendarEvent :: get_repeat_options();

        $repeat_elements = array();
        $repeat_elements[] = $this->createElement('select', CalendarEvent :: PROPERTY_REPEAT_TYPE, null, $options);
        $repeat_elements[] = $this->createElement('static', null, null, Translation :: get('Until'));
        $repeat_elements[] = $this->createElement('datepicker', CalendarEvent :: PROPERTY_REPEAT_TO, '', array('form_name' => $this->getAttribute('name'), 'class' => CalendarEvent :: PROPERTY_REPEAT_TO), true);
        $this->addGroup($repeat_elements, self :: PARAM_REPEAT_DATE, null, '&nbsp;', false);
        $this->addGroupRule(self :: PARAM_REPEAT_DATE, array(CalendarEvent :: PROPERTY_REPEAT_TO => array(array(Translation :: get('InvalidDate'), 'date'))));

        $this->addElement('html', '</div>');
        $this->addElement('html', "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var expiration = document.getElementById('" . self :: PARAM_REPEAT . "');
					if (expiration.checked)
					{
						timewindow_hide('repeat_timewindow');
					}
					function timewindow_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function timewindow_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");
        $this->addElement('category');
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/dates.js'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/doubletimepicker.js'));
    }

    // Inherited
    function setDefaults($defaults = array ())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[CalendarEvent :: PROPERTY_START_DATE] = $lo->get_start_date();
            $defaults[CalendarEvent :: PROPERTY_END_DATE] = $lo->get_end_date();

            if ($this->form_type == self :: TYPE_EDIT)
            {
                $repeats = $lo->repeats();
                if (! $repeats)
                {
                    $defaults[self :: PARAM_REPEAT] = 0;
                }
                else
                {
                    $defaults[self :: PARAM_REPEAT] = 1;
                    $defaults[CalendarEvent :: PROPERTY_REPEAT_TYPE] = $lo->get_repeat_type();
                    $defaults[CalendarEvent :: PROPERTY_REPEAT_TO] = $lo->get_repeat_to();
                }
            }
            else
            {
                $defaults[self :: PARAM_REPEAT] = 0;
            }
        }
        else
        {
            $defaults[self :: PARAM_REPEAT] = 0;
        }

        parent :: setDefaults($defaults);
    }

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[CalendarEvent :: PROPERTY_START_DATE] = $valuearray[3];
        $defaults[CalendarEvent :: PROPERTY_END_DATE] = $valuearray[4];
        parent :: set_values($defaults);
    }

    // Inherited
    function create_content_object()
    {
        $object = new CalendarEvent();
        $values = $this->exportValues();
        $object->set_start_date(Utilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_START_DATE]));
        $object->set_end_date(Utilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_END_DATE]));

        if ($values[self :: PARAM_REPEAT] == 0)
        {
            $object->set_repeat_type(0);
            $object->set_repeat_to(0);
        }
        else
        {
            $object->set_repeat_type($values[CalendarEvent :: PROPERTY_REPEAT_TYPE]);
            $to_date = Utilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_REPEAT_TO]);
            $object->set_repeat_to($to_date);
        }

        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    // Inherited
    function update_content_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        $object->set_start_date(Utilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_START_DATE]));
        $object->set_end_date(Utilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_END_DATE]));

        if ($values[self :: PARAM_REPEAT] == 0)
        {
            $object->set_repeat_type(0);
            $object->set_repeat_to(0);
        }
        else
        {
            $object->set_repeat_type($values[CalendarEvent :: PROPERTY_REPEAT_TYPE]);
            $to_date = Utilities :: time_from_datepicker($values[CalendarEvent :: PROPERTY_REPEAT_TO]);
            $object->set_repeat_to($to_date);
        }

        return parent :: update_content_object();
    }
}
?>
