<?php
/**
 * $Id: task_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.task
 */
require_once dirname(__FILE__) . '/task.class.php';
/**
 * This class represents a form to create or update task
 */
class TaskForm extends ContentObjectForm
{
    const TOTAL_PROPERTIES = 5;
    const PARAM_REPEAT = 'repeated';
    const PARAM_REPEAT_DATE = 'repeate_date';
     
    const PARAM_PRIORITY = 'priority';
    const PARAM_TYPE = 'type';
    

    // Inherited
    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        
        $options_priority = Task :: get_priority_options();
        $choices_priority = array();
        $choices_priority[] = $this->createElement('select', Task :: PROPERTY_TASK_PRIORITY, null, $options_priority); 	
        $this->addGroup($choices_priority, self :: PARAM_PRIORITY, Translation :: get('Priority'), '<br />', false);
        
        $options_type = Task :: get_types_options();
        $choices_type = array();
        $choices_type[] = $this->createElement('select', Task :: PROPERTY_TASK_TYPE, null, $options_type); 	
        $this->addGroup($choices_type, self :: PARAM_TYPE, Translation :: get('TaskType'), '<br />', false);

        $this->add_timewindow(Task :: PROPERTY_START_DATE, Task :: PROPERTY_END_DATE, Translation :: get('StartTimeWindow'), Translation :: get('EndTimeWindow'));
        
        $choices[] = $this->createElement('radio', self :: PARAM_REPEAT, '', Translation :: get('No'), 0, array('onclick' => 'javascript:timewindow_hide(\'repeat_timewindow\')', 'id' => self :: PARAM_REPEAT));
        $choices[] = $this->createElement('radio', self :: PARAM_REPEAT, '', Translation :: get('Yes'), 1, array('onclick' => 'javascript:timewindow_show(\'repeat_timewindow\')'));
        $this->addGroup($choices, null, Translation :: get('Repeat'), '<br />', false);
        $this->addElement('html', '<div style="padding-left: 25px; display: block;" id="repeat_timewindow">');
        
        $options = Task :: get_repeat_options();

        $repeat_elements = array();
        $repeat_elements[] = $this->createElement('select', Task :: PROPERTY_REPEAT_TYPE, null, $options);
        $repeat_elements[] = $this->createElement('static', null, null, Translation :: get('Until'));
        $repeat_elements[] = $this->createElement('datepicker', Task :: PROPERTY_REPEAT_TO, '', array('form_name' => $this->getAttribute('name'), 'class' => Task :: PROPERTY_REPEAT_TO), true);
        $this->addGroup($repeat_elements, self :: PARAM_REPEAT_DATE, null, '&nbsp;', false);
        $this->addGroupRule(self :: PARAM_REPEAT_DATE, array(Task :: PROPERTY_REPEAT_TO => array(array(Translation :: get('InvalidDate'), 'date'))));

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
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        
        $options_priority = Task :: get_priority_options();
        $choices_priority = array();
        $choices_priority[] = $this->createElement('select', Task :: PROPERTY_TASK_PRIORITY, null, $options_priority); 	
        $this->addGroup($choices_priority, self :: PARAM_PRIORITY, Translation :: get('Priority'), '<br />', false);
        
        $options_type = Task :: get_types_options();
        $choices_type = array();
        $choices_type[] = $this->createElement('select', Task :: PROPERTY_TASK_TYPE, null, $options_type); 	
        $this->addGroup($choices_type, self :: PARAM_TYPE, Translation :: get('TaskType'), '<br />', false);

        $this->add_timewindow(Task :: PROPERTY_START_DATE, Task :: PROPERTY_END_DATE, Translation :: get('StartTimeWindow'), Translation :: get('EndTimeWindow'));
        
        $choices[] = $this->createElement('radio', self :: PARAM_REPEAT, '', Translation :: get('No'), 0, array('onclick' => 'javascript:timewindow_hide(\'repeat_timewindow\')', 'id' => self :: PARAM_REPEAT));
        $choices[] = $this->createElement('radio', self :: PARAM_REPEAT, '', Translation :: get('Yes'), 1, array('onclick' => 'javascript:timewindow_show(\'repeat_timewindow\')'));
        $this->addGroup($choices, null, Translation :: get('Repeat'), '<br />', false);
        $this->addElement('html', '<div style="padding-left: 25px; display: block;" id="repeat_timewindow">');
        
        $options = Task :: get_repeat_options();

        $repeat_elements = array();
        $repeat_elements[] = $this->createElement('select', Task :: PROPERTY_REPEAT_TYPE, null, $options);
        $repeat_elements[] = $this->createElement('static', null, null, Translation :: get('Until'));
        $repeat_elements[] = $this->createElement('datepicker', Task :: PROPERTY_REPEAT_TO, '', array('form_name' => $this->getAttribute('name'), 'class' => Task :: PROPERTY_REPEAT_TO), true);
        $this->addGroup($repeat_elements, self :: PARAM_REPEAT_DATE, null, '&nbsp;', false);
        $this->addGroupRule(self :: PARAM_REPEAT_DATE, array(Task :: PROPERTY_REPEAT_TO => array(array(Translation :: get('InvalidDate'), 'date'))));

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
            $defaults[Task :: PROPERTY_TASK_TYPE] = $lo->get_task_type();
            $defaults[Task :: PROPERTY_TASK_PRIORITY] = $lo->get_task_priority();
        	$defaults[Task :: PROPERTY_START_DATE] = $lo->get_start_date();
            $defaults[Task :: PROPERTY_END_DATE] = $lo->get_end_date();

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
                    $defaults[Task :: PROPERTY_REPEAT_TYPE] = $lo->get_repeat_type();
                    $defaults[Task :: PROPERTY_REPEAT_TO] = $lo->get_repeat_to();
                }
            }
            else
            {
                $defaults[self :: PARAM_REPEAT] = 0;
                $defaults[Task :: PROPERTY_START_DATE] = time();
            	$defaults[Task :: PROPERTY_END_DATE] = strtotime('+1 Hour', time());
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
        $defaults[Task :: PROPERTY_TASK_TYPE] = $valuearray[3];
        $defaults[Task :: PROPERTY_TASK_PRIORITY] = $valuearray[4];
        $defaults[Task :: PROPERTY_START_DATE] = $valuearray[5];
        $defaults[Task :: PROPERTY_END_DATE] = $valuearray[6];
        
        parent :: set_values($defaults);
    }

    // Inherited
    function create_content_object()
    {
        $object = new Task();
        $values = $this->exportValues();
        $object->set_task_type($values[Task :: PROPERTY_TASK_TYPE]);
        $object->set_task_priority($values[Task :: PROPERTY_TASK_PRIORITY]);
        $object->set_start_date(Utilities :: time_from_datepicker($values[Task :: PROPERTY_START_DATE]));
        $object->set_end_date(Utilities :: time_from_datepicker($values[Task :: PROPERTY_END_DATE]));
        if ($values[self :: PARAM_REPEAT] == 0)
        {
            $object->set_repeat_type(0);
            $object->set_repeat_to(0);
        }
        else
        {
            $object->set_repeat_type($values[Task :: PROPERTY_REPEAT_TYPE]);
            $to_date = Utilities :: time_from_datepicker($values[Task :: PROPERTY_REPEAT_TO]);
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
        $object->set_task_type($values[Task :: PROPERTY_TASK_TYPE]);
        $object->set_task_priority($values[Task :: PROPERTY_TASK_PRIORITY]);
        $object->set_start_date(Utilities :: time_from_datepicker($values[Task :: PROPERTY_START_DATE]));
        $object->set_end_date(Utilities :: time_from_datepicker($values[Task :: PROPERTY_END_DATE]));
        if ($values[self :: PARAM_REPEAT] == 0)
        {
            $object->set_repeat_type(0);
            $object->set_repeat_to(0);
        }
        else
        {
            $object->set_repeat_type(Utilities :: $values[Task :: PROPERTY_REPEAT_TYPE]);
            $to_date = Utilities :: time_from_datepicker($values[Task :: PROPERTY_REPEAT_TO]);
            $object->set_repeat_to($to_date);
        }

        return parent :: update_content_object();
    }
}
?>