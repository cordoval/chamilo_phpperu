<?php
/**
 * $Id: assessment_select_question_form.class.php $
 * @package repository.lib.content_object.select_question
 */
require_once PATH :: get_repository_path() . '/question_types/select_question/select_question_form.class.php';
require_once dirname(__FILE__) . '/assessment_select_question_option.class.php';

class AssessmentSelectQuestionForm extends SelectQuestionForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/assessment_select_question.js'));
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/assessment_select_question.js'));
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
                    $defaults[SelectQuestionOption :: PROPERTY_VALUE][$index] = $option->get_value();
                    $defaults[AssessmentSelectQuestionOption :: PROPERTY_SCORE][$index] = $option->get_score();
                    $defaults[AssessmentSelectQuestionOption :: PROPERTY_FEEDBACK][$index] = $option->get_feedback();
                    if ($object->get_answer_type() == 'checkbox')
                    {
                        $defaults[AssessmentSelectQuestionOption :: PROPERTY_CORRECT][$index] = $option->is_correct();
                    }
                    elseif ($option->is_correct())
                    {
                        $defaults[AssessmentSelectQuestionOption :: PROPERTY_CORRECT] = $index;
                    }
                }
            }
            else
            {
                $number_of_options = intval($_SESSION['select_number_of_options']);

                for($option_number = 0; $option_number < $number_of_options; $option_number ++)
                {
                    $defaults[AssessmentSelectQuestionOption :: PROPERTY_SCORE][$option_number] = 1;
                }
            }
        }
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $object = new AssessmentSelectQuestion();
        return parent :: create_content_object($object);
    }

    function validate_selected_answers($fields)
    {
        if (! isset($fields[AssessmentSelectQuestionOption :: PROPERTY_CORRECT]))
        {
            $message = $_SESSION['select_answer_type'] == 'checkbox' ? Translation :: get('SelectAtLeastOneCorrectAnswer') : Translation :: get('SelectACorrectAnswer');
            return array('change_answer_type' => $message);
        }
        return true;
    }

    function add_options_to_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        $options = array();
        foreach ($values[SelectQuestionOption :: PROPERTY_VALUE] as $option_id => $value)
        {
            $score = $values[AssessmentSelectQuestionOption :: PROPERTY_SCORE][$option_id];
            $feedback = $values[AssessmentSelectQuestionOption :: PROPERTY_FEEDBACK][$option_id];
            if ($_SESSION['select_answer_type'] == 'radio')
            {
                $correct = $values[AssessmentSelectQuestionOption :: PROPERTY_CORRECT] == $option_id;
            }
            else
            {
                $correct = $values[AssessmentSelectQuestionOption :: PROPERTY_CORRECT][$option_id];
            }
            $options[] = new AssessmentSelectQuestionOption($value, $correct, $score, $feedback);
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

        $this->addElement('hidden', 'select_answer_type', $_SESSION['select_answer_type'], array('id' => 'select_answer_type'));
        $this->addElement('hidden', 'select_number_of_options', $_SESSION['select_number_of_options'], array('id' => 'select_number_of_options'));

        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', 'change_answer_type', $switch_label, array('class' => 'normal switch', 'id' => 'change_answer_type'));
        //Notice: The [] are added to this element name so we don't have to deal with the _x and _y suffixes added when clicking an image button
        $buttons[] = $this->createElement('style_button', 'add[]', Translation :: get('AddSelectOption'), array('class' => 'normal add', 'id' => 'add_option'));
        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['toolbar'] = 'RepositoryQuestion';
        $this->addElement('html', implode("\n", $table_header));

        $table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="checkbox"></th>';
        $table_header[] = '<th style="width: 320px;">' . Translation :: get('Answer') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $table_header[] = '<th class="numeric">' . Translation :: get('Score') . '</th>';
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

                if ($_SESSION['select_answer_type'] == 'checkbox')
                {
                    $group[] = & $this->createElement('checkbox', AssessmentSelectQuestionOption :: PROPERTY_CORRECT . '[' . $option_number . ']', Translation :: get('Correct'), '', array('class' => SelectQuestionOption :: PROPERTY_VALUE, 'id' => AssessmentSelectQuestionOption :: PROPERTY_CORRECT . '[' . $option_number . ']'));
                }
                else
                {
                    $group[] = & $this->createElement('radio', AssessmentSelectQuestionOption :: PROPERTY_CORRECT, Translation :: get('Correct'), '', $option_number, array('class' => SelectQuestionOption :: PROPERTY_VALUE, 'id' => AssessmentSelectQuestionOption :: PROPERTY_CORRECT . '[' . $option_number . ']'));
                }

                $group[] = & $this->createElement('text', SelectQuestionOption :: PROPERTY_VALUE . '[' . $option_number . ']', Translation :: get('Answer'), array('style' => 'width: 300px;'));
                $group[] = & $this->create_html_editor(AssessmentSelectQuestionOption :: PROPERTY_FEEDBACK . '[' . $option_number . ']', Translation :: get('Feedback'), $html_editor_options);
                $group[] = & $this->createElement('text', AssessmentSelectQuestionOption :: PROPERTY_SCORE . '[' . $option_number . ']', Translation :: get('Score'), 'size="2"  class="input_numeric"');

                if ($number_of_options - count($_SESSION['select_skip_options']) > 2)
                {
                    $group[] = & $this->createElement('image', 'remove[' . $option_number . ']', Theme :: get_common_image_path() . 'action_delete.png', array('class' => 'remove_option', 'id' => 'remove_' . $option_number));
                }
                else
                {
                    $group[] = & $this->createElement('static', null, null, '<img class="remove_option" src="' . Theme :: get_common_image_path() . 'action_delete_na.png" class="remove_option" />');
                }

                $this->addGroup($group, SelectQuestionOption :: PROPERTY_VALUE . '_' . $option_number, null, '', false);

                $renderer->setElementTemplate('<tr id="option_' . $option_number . '" class="' . ($option_number % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', SelectQuestionOption :: PROPERTY_VALUE . '_' . $option_number);
                $renderer->setGroupElementTemplate('<td>{element}</td>', SelectQuestionOption :: PROPERTY_VALUE . '_' . $option_number);
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
?>