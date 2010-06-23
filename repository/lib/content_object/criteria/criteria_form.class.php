<?php
/**
 *  $Id: criteria_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 *  @package repository.lib.content_object.criteria
 *  @author Sven Vanpoucke
 *
 */
require_once dirname(__FILE__) . '/criteria.class.php';
/**
 * This class represents a form to create or update criterias
 */
class CriteriaForm extends ContentObjectForm
{

	protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Options'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/criteria.js'));
        $this->add_options();
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Options'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/criteria.js'));
        $this->add_options();
        $this->addElement('category');
    }

	function setDefaults($defaults = array ())
    {
        if (! $this->isSubmitted())
        {
            $object = $this->get_content_object();
            if (! is_null($object))
            {
                $options = $object->get_options();
                foreach ($options as $index => $option)
                {
                    $defaults['options'][$index] = $option->get_description();
                    $defaults['scores'][$index] = $option->get_score();
                }
            }
            else
            {
                $number_of_options = intval($_SESSION['criteria_number_of_options']);

                for($option_number = 0; $option_number < $number_of_options; $option_number ++)
                {
                    $defaults['scores'][$option_number] = 1;
                }
            }
        }
        //print_r($defaults);
        parent :: setDefaults($defaults);
    }

    // Inherited
    function create_content_object()
    {
        $object = new Criteria();
        $this->set_content_object($object);
        $this->add_options_to_object();
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $this->add_options_to_object();
        return parent :: update_content_object();
    }

	function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']))
        {
            return false;
        }
        return parent :: validate();
    }

	function add_options_to_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        $options = array();
        foreach ($values['options'] as $option_id => $description)
        {
            $options[] = new CriteriaOption($description, $values['scores'][$option_id]);
        }
        $object->set_options($options);
    }

	/**
     * Adds the form-fields to the form to provide the possible options for this
     * multiple choice question
     */
    function add_options()
    {
        $renderer = $this->defaultRenderer();

        if (! $this->isSubmitted())
        {
            unset($_SESSION['criteria_number_of_options']);
            unset($_SESSION['criteria_skip_options']);
        }

        if (! isset($_SESSION['criteria_number_of_options']))
        {
            $_SESSION['criteria_number_of_options'] = 3;
        }

        if (! isset($_SESSION['criteria_skip_options']))
        {
            $_SESSION['criteria_skip_options'] = array();
        }

        if (isset($_POST['add']))
        {
            $_SESSION['criteria_number_of_options'] = $_SESSION['criteria_number_of_options'] + 1;
        }

        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['criteria_skip_options'][] = $indexes[0];
        }

        $object = $this->get_content_object();
        if (! $this->isSubmitted() && $object->get_number_of_options() != 0)
        {
            $_SESSION['criteria_number_of_options'] = $object->get_number_of_options();
        }
        $number_of_options = intval($_SESSION['criteria_number_of_options']);

        $this->addElement('hidden', 'criteria_number_of_options', $_SESSION['criteria_number_of_options'], array('id' => 'criteria_number_of_options'));

        $buttons = array();
        $buttons[] = $this->createElement('style_button', 'add[]', Translation :: get('AddOption'), array('class' => 'normal add add_option'));
        $this->addGroup($buttons, 'criteria_buttons', null, '', false);

        $table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="checkbox"></th>';
        $table_header[] = '<th>' . Translation :: get('Options') . '</th>';
        $table_header[] = '<th class="numeric">' . Translation :: get('Score') . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));

        $counter = 1;

        for($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['criteria_skip_options']))
            {
                $group = array();

                $group[] = & $this->createElement('static', null, null, $counter . '.');
                $group[] = $this->createElement('text', 'options[' . $option_number . ']', Translation :: get('Option'), array('style' => 'width: 99%;'));
                $group[] = $this->createElement('text', 'scores[' . $option_number . ']', Translation :: get('Option'), array('size' => '2', 'class' => 'input_numeric'));

                if ($number_of_options - count($_SESSION['criteria_skip_options']) > 2)
                {
                    $group[] = & $this->createElement('image', 'remove[' . $option_number . ']', Theme :: get_common_image_path() . 'action_delete.png', array('class' => 'remove_option', 'id' => 'remove_' . $option_number));
                }
                else
                {
                    $group[] = & $this->createElement('static', null, null, '<img id="remove_' . $option_number . '" class="remove_option" src="' . Theme :: get_common_image_path() . 'action_delete_na.png" />');
                }

                $this->addGroup($group, 'options_' . $option_number, null, '', false);

                $this->addGroupRule('options_' . $option_number, array(
                		'options[' . $option_number . ']' => array(array(Translation :: get('ThisFieldIsRequired'), 'required')),
                		'scores[' . $option_number . ']' => array(array(Translation :: get('ThisFieldIsRequired'), 'required'), array(Translation :: get('ValueShouldBeNumeric'), 'numeric'))));

                $renderer->setElementTemplate('<tr id="options_' . $option_number . '" class="' . ($option_number % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'options_' . $option_number);
                $renderer->setGroupElementTemplate('<td>{element}</td>', 'options_' . $option_number);

                //$defaults['scores[' . $option_number . ']'] = 1;

                $counter++;
            }
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode("\n", $table_footer));

        $this->addGroup($buttons, 'criteria_buttons', null, '', false);

        $renderer->setElementTemplate('<div style="margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>', 'criteria_buttons');
        $renderer->setGroupElementTemplate('<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 'criteria_buttons');

        $this->setDefaults($defaults);
    }
}
?>