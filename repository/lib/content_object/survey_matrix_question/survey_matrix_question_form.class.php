<?php
/**
 * $Id: survey_matrix_question_form.class.php
 * @package repository.lib.content_object.survey_matrix_question
 */

require_once PATH :: get_repository_path() . '/question_types/matrix_question/matrix_question_form.class.php';

class SurveyMatrixQuestionForm extends MatrixQuestionForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/survey_matrix_question.js'));
       
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/survey_matrix_question.js'));
    }
	    
    function create_content_object()
    {
        $object = new SurveyMatrixQuestion();
        return parent :: create_content_object($object);
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
        $table_header[] = '<th>' . Translation :: get('Options') . '</th>';
//        $table_header[] = '<th class="code">' . Translation :: get('Matches') . '</th>';
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
//                $group[] = $this->createElement('select', MatrixQuestionOption::PROPERTY_MATCHES . '[' . $option_number . ']', Translation :: get('Matches'), $matches, array('class' => 'option_matches'));
//                $group[2]->setMultiple($multiple);
                
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
