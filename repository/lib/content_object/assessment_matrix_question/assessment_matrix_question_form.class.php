<?php
/**
 * $Id: assessment_matrix_question_form.class.php $
 * @package repository.lib.content_object.matrix_question
 */
require_once PATH :: get_repository_path(). '/question_types/matrix_question/matrix_question_form.class.php';
require_once dirname(__FILE__) . '/assessment_matrix_question_option.class.php';

class AssessmentMatrixQuestionForm extends MatrixQuestionForm
{
    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        if (! is_null($object))
        {
            $options = $object->get_options();
            foreach ($options as $index => $option)
            {
                $defaults[MatrixQuestionOption::PROPERTY_VALUE][$index] = $option->get_value();
                $defaults[AssessmentMatrixQuestionOption::PROPERTY_SCORE][$index] = $option->get_score();
                $defaults[MatrixQuestionOption::PROPERTY_MATCHES][$index] = $option->get_matches();
                $defaults[AssessmentMatrixQuestionOption::PROPERTY_FEEDBACK][$index] = $option->get_feedback();
            }
            $matches = $object->get_matches();
            foreach ($matches as $index => $match)
            {
                $defaults['match'][$index] = $match;
            }
        }
        else
        {
            $number_of_options = intval($_SESSION['mq_number_of_options']);
            
            for($option_number = 0; $option_number < $number_of_options; $option_number ++)
            {
                $defaults[AssessmentMatrixQuestionOption::PROPERTY_SCORE][$option_number] = 1;
            }
        }
        
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $object = new AssessmentMatrixQuestion();
        return parent :: create_content_object($object);
    }

    /**
     * Adds the answer to the current learning object.
     * This function adds the list of possible options and matches and the
     * relation between the options and the matches to the question.
     */
    function add_answers()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        $options = array();
        $matches = array();
        
        foreach ($values[MatrixQuestionOption::PROPERTY_VALUE] as $option_id => $value)
        {
            //Create the option with it corresponding match
            $options[] = new AssessmentMatrixQuestionOption($value, serialize($_POST[MatrixQuestionOption::PROPERTY_MATCHES][$option_id]), $values[AssessmentMatrixQuestionOption::PROPERTY_SCORE][$option_id], $values[AssessmentMatrixQuestionOption::PROPERTY_FEEDBACK][$option_id]);
        }
        
        foreach ($values['match'] as $match)
        {
            $matches[] = $match;
        }
        $object->set_options($options);
        $object->set_matches($matches);
        $object->set_matrix_type($_SESSION['mq_matrix_type']);
    }

    /**
     * Adds the form-fields to the form to provide the possible options for this
     * multiple choice question
     * @todo Add rules to require options and matches
     */
    function add_options()
    {
        $number_of_options = intval($_SESSION['mq_number_of_options']);
        $matches = array();
        $match_label = 'A';
        
        for($match_number = 0; $match_number < $_SESSION['mq_number_of_matches']; $match_number ++)
        {
            if (! in_array($match_number, $_SESSION['mq_skip_matches']))
            {
                $matches[$match_number] = $match_label ++;
            }
        }
        
        $this->addElement('category', Translation :: get('Options'));
        
        if ($_SESSION['mq_matrix_type'] == MatrixQuestion :: MATRIX_TYPE_RADIO)
        {
            $switch_label = Translation :: get('SwitchToMultipleMatches');
            $multiple = false;
        }
        elseif ($_SESSION['mq_matrix_type'] == MatrixQuestion :: MATRIX_TYPE_CHECKBOX)
        {
            $switch_label = Translation :: get('SwitchToSingleMatch');
            $multiple = true;
        }
        
        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', 'change_matrix_type[]', $switch_label, array('class' => 'normal switch change_matrix_type'));
        $buttons[] = $this->createElement('style_button', 'add_option[]', Translation :: get('AddMatrixQuestionOption'), array('class' => 'normal add', 'id' => 'add_option'));
        $this->addGroup($buttons, 'question_buttons', null, '', false);
        
        $renderer = $this->defaultRenderer();
        
        $table_header = array();
        $table_header[] = '<table class="data_table options">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th>' . Translation :: get('Answer') . '</th>';
        $table_header[] = '<th class="code">' . Translation :: get('Matches') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $table_header[] = '<th class="numeric">' . Translation :: get('Score') . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));
        
        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['show_toolbar'] = false;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';
        
        $visual_number = 0;
        
        for($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            $group = array();
            if (! in_array($option_number, $_SESSION['mq_skip_options']))
            {
                $visual_number ++;
                $group[] = $this->createElement('static', null, null, $visual_number);
                $group[] = $this->create_html_editor(MatrixQuestionOption::PROPERTY_VALUE . '[' . $option_number . ']', Translation :: get('Answer'), $html_editor_options);
                $group[] = $this->createElement('select', MatrixQuestionOption::PROPERTY_MATCHES . '[' . $option_number . ']', Translation :: get('Matches'), $matches, array('class' => 'option_matches'));
                $group[2]->setMultiple($multiple);
                $group[] = $this->create_html_editor(AssessmentMatrixQuestionOption::PROPERTY_FEEDBACK . '[' . $option_number . ']', Translation :: get('Feedback'), $html_editor_options);
                $group[] = $this->createElement('text', AssessmentMatrixQuestionOption::PROPERTY_SCORE . '[' . $option_number . ']', Translation :: get('Score'), 'size="2"  class="input_numeric"');
                
                if ($number_of_options - count($_SESSION['mq_skip_options']) > 2)
                {
                    $group[] = $this->createElement('image', 'remove_option[' . $option_number . ']', Theme :: get_common_image_path() . 'action_delete.png', array('class' => 'remove_option', 'id' => 'remove_option_' . $option_number));
                }
                else
                {
                    $group[] = & $this->createElement('static', null, null, '<img class="remove_option" src="' . Theme :: get_common_image_path() . 'action_delete_na.png" />');
                }
                
                $this->addGroup($group, MatrixQuestionOption::PROPERTY_VALUE . '_' . $option_number, null, '', false);
                
                $renderer->setElementTemplate('<tr id="option_' . $option_number . '" class="' . ($visual_number % 2 == 0 ? 'row_odd' : 'row_even') . '">{element}</tr>', MatrixQuestionOption::PROPERTY_VALUE . '_' . $option_number);
                $renderer->setGroupElementTemplate('<td>{element}</td>', MatrixQuestionOption::PROPERTY_VALUE . '_' . $option_number);
                
                $this->addGroupRule(MatrixQuestionOption::PROPERTY_VALUE . '_' . $option_number, array(MatrixQuestionOption::PROPERTY_VALUE . '[' . $option_number . ']' => array(array(Translation :: get('ThisFieldIsRequired'), 'required')), AssessmentMatrixQuestionOption::PROPERTY_SCORE . '[' . $option_number . ']' => array(array(Translation :: get('ThisFieldIsRequired'), 'required'), array(Translation :: get('ValueShouldBeNumeric'), 'numeric'))));
            }
        }
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode("\n", $table_footer));
        
        $this->addGroup($buttons, 'question_buttons', null, '', false);
        
        $renderer->setElementTemplate('<div style="margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>', 'question_buttons');
        $renderer->setGroupElementTemplate('<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 'question_buttons');
        
        $this->addElement('category');
    }
}
?>
