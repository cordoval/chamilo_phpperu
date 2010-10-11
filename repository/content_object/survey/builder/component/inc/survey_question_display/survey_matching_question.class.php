<?php
/**
 * $Id: survey_matching_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.viewer.wizard.inc.question_display
 */
require_once dirname(__FILE__) . '/../survey_question_display.class.php';

class SurveyMatchingQuestionDisplay extends SurveyQuestionDisplay
{
    private $matches;
    private $answers;

    function add_question_form()
    {
        //$this->answers = $this->shuffle_with_keys($this->get_question()->get_options());
        $this->answers = $this->get_question()->get_options();
        $this->matches = $this->prepare_matches($this->get_question()->get_matches());
        
        $this->add_matches();
        $this->add_answers();
    }

    function add_matches()
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        
        $table_header = array();
        $table_header[] = '<table class="data_table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th>' . Translation :: get('PossibleMatches') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode("\n", $table_header));
        
        $matches = $this->matches;
        
        $match_label = 'A';
        foreach ($matches as $index => $match)
        {
            $group = array();
            $group[] = $formvalidator->createElement('static', null, null, $match_label);
            $group[] = $formvalidator->createElement('static', null, null, $match);
            $formvalidator->addGroup($group, 'match_' . $match_label, null, '', false);
            
            $renderer->setElementTemplate('<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'match_' . $match_label);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'match_' . $match_label);
            $match_label ++;
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode("\n", $table_footer));
    }

    function add_answers()
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        
        $table_header = array();
        $table_header[] = '<table class="data_table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th colspan="2">' . Translation :: get('ChooseYourOptionMatch') . '</th>';
        //		$table_header[] = '<th></th>';
        //		$table_header[] = '<th>' . Translation :: get('Options') . '</th>';
        //		$table_header[] = '<th>' . Translation :: get('Matches') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode("\n", $table_header));
        
        $question_id = $this->get_clo_question()->get_id();
        
        $answers = $this->answers;
        $matches = $this->matches;
        
        $options = array();
        $match_label = 'A';
        foreach ($matches as $index => $match)
        {
            $options[$index] = $match_label;
            $match_label ++;
        }
        
        $answer_count = 0;
        foreach ($answers as $answer_id => $answer)
        {
            $answer_name = $question_id . '_' . $answer_id.'_'.$this->get_page_nr();
            
            $group = array();
            $answer_number = ($answer_count + 1) . '.';
            $group[] = $formvalidator->createElement('static', null, null, $answer_number);
            $group[] = $formvalidator->createElement('static', null, null, $answer->get_value());
            $group[] = $formvalidator->createElement('select', $answer_name, null, $options);
            
            $formvalidator->addGroup($group, 'group_' . $answer_name, null, '', false);
            
            $renderer->setElementTemplate('<tr class="' . ($answer_count % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'group_' . $answer_name);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'group_' . $answer_name);
            $answer_count ++;
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode("\n", $table_footer));
    }

    function prepare_matches($matches)
    {
        //$matches = $this->shuffle_with_keys($matches);
        return $matches;
    }

    function get_instruction()
    {
        return Translation :: get('SelectCorrectAnswers');
    }
}
?>