<?php
namespace repository\content_object\survey_select_question;

use repository\ContentObjectForm;

use common\libraries\Path;
use common\libraries\ResourceManager;

/**
 * @package repository.content_object.survey_select_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class SurveySelectQuestionForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Options'));
        $this->add_options();
        $this->addElement('category');
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'repository/content_object/survey_select_question/resources/javascript/survey_select_question.js'));
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get('Options'));
        $this->add_options();
        $this->addElement('category');
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'repository/content_object/survey_select_question/resources/javascript/survey_select_question.js'));
    }

    function setDefaults($defaults = array ())
    {
        if (! $this->isSubmitted())
        {
            $object = $this->get_content_object();
            if ($object->get_number_of_options() != 0)
            {
                $options = $object->get_options();
                foreach ($options as $index => $option)
                {
                    $defaults[SurveySelectQuestionOption :: PROPERTY_VALUE][$index] = $option->get_value();
                }
            }
            else
            {
                $number_of_options = intval($_SESSION['select_number_of_options']);
            }
        }
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $object = new SurveySelectQuestion();
        $this->set_content_object($object);
        $this->add_options_to_object();
        return parent :: create_content_object($object);
    }

    function update_content_object()
    {
        $this->add_options_to_object();
        return parent :: update_content_object();
    }

    function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']) || isset($_POST['change_answer_type']))
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
        foreach ($values[SurveySelectQuestionOption :: PROPERTY_VALUE] as $option_id => $value)
        {
            $options[] = new SurveySelectQuestionOption($value);
        }
        $object->set_answer_type($_SESSION['select_answer_type']);
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
            unset($_SESSION['select_number_of_options']);
            unset($_SESSION['select_skip_options']);
            unset($_SESSION['select_answer_type']);
        }
        if (! isset($_SESSION['select_number_of_options']))
        {
            $_SESSION['select_number_of_options'] = 3;
        }
        if (! isset($_SESSION['select_skip_options']))
        {
            $_SESSION['select_skip_options'] = array();
        }
        if (! isset($_SESSION['select_answer_type']))
        {
            $_SESSION['select_answer_type'] = 'radio';
        }
        if (isset($_POST['add']))
        {
            $_SESSION['select_number_of_options'] = $_SESSION['select_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['select_skip_options'][] = $indexes[0];
        }
        if (isset($_POST['change_answer_type']))
        {
            $_SESSION['select_answer_type'] = $_SESSION['select_answer_type'] == 'radio' ? 'checkbox' : 'radio';
        }
        $object = $this->get_content_object();
        if (! $this->isSubmitted() && $object->get_number_of_options() != 0)
        {
            $_SESSION['select_number_of_options'] = $object->get_number_of_options();
            $_SESSION['select_answer_type'] = $object->get_answer_type();
        }
        $number_of_options = intval($_SESSION['select_number_of_options']);

        if ($_SESSION['select_answer_type'] == 'radio')
        {
            $switch_label = Translation :: get('SwitchToMultipleSelect');
        }
        elseif ($_SESSION['select_answer_type'] == 'checkbox')
        {
            $switch_label = Translation :: get('SwitchToSingleSelect');
        }

        $this->addElement('hidden', 'select_answer_type', $_SESSION['select_answer_type'], array(
                'id' => 'select_answer_type'));
        $this->addElement('hidden', 'select_number_of_options', $_SESSION['select_number_of_options'], array(
                'id' => 'select_number_of_options'));

        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', 'change_answer_type', $switch_label, array(
                'class' => 'normal switch',
                'id' => 'change_answer_type'));
        //Notice: The [] are added to this element name so we don't have to deal with the _x and _y suffixes added when clicking an image button
        $buttons[] = $this->createElement('style_button', 'add[]', Translation :: get('AddSelectOption'), array(
                'class' => 'normal add',
                'id' => 'add_option'));
        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $html_editor_options = array();
        $html_editor_options['style'] = 'width: 100%; height: 65px;';
        $html_editor_options['toolbar'] = 'RepositoryQuestion';

        $table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th style="width: 320px;">' . Translation :: get('Options') . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));

        for($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['select_skip_options']))
            {
                $group = array();
                $group[] = & $this->createElement('text', SurveySelectQuestionOption :: PROPERTY_VALUE . '[' . $option_number . ']', Translation :: get('Answer'), array(
                        'style' => 'width: 300px;'));

                if ($number_of_options - count($_SESSION['select_skip_options']) > 2)
                {
                    $group[] = & $this->createElement('image', 'remove[' . $option_number . ']', Theme :: get_common_image_path() . 'action_delete.png', array(
                            'class' => 'remove_option',
                            'id' => 'remove_' . $option_number));
                }
                else
                {
                    $group[] = & $this->createElement('static', null, null, '<img class="remove_option" src="' . Theme :: get_common_image_path() . 'action_delete_na.png" />');
                }

                $this->addGroup($group, SurveySelectQuestionOption :: PROPERTY_VALUE . '_' . $option_number, null, '', false);

                $renderer->setElementTemplate('<tr id="option_' . $option_number . '" class="' . ($option_number % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', SurveySelectQuestionOption :: PROPERTY_VALUE . '_' . $option_number);
                $renderer->setGroupElementTemplate('<td>{element}</td>', SurveySelectQuestionOption :: PROPERTY_VALUE . '_' . $option_number);
            }
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode("\n", $table_footer));

        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer->setElementTemplate('<div style="margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>', 'question_buttons');
        $renderer->setGroupElementTemplate('<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 'question_buttons');
    }
}