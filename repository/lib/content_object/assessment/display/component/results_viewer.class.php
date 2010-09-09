<?php
/**
 * $Id: result_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */
require_once dirname(__FILE__) . '/../assessment_display.class.php';
require_once dirname(__FILE__) . '/result_viewer/question_result_display.class.php';

class AssessmentDisplayResultsViewerComponent extends AssessmentDisplay
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $form = new FormValidator('result_viewer', 'post', $this->get_url());
        $rdm = RepositoryDataManager :: get_instance();
        
        $results = $this->get_parent()->retrieve_assessment_results();
        $question_cids = array_keys($results);
        
        $this->display_header();
        
        if (count($question_cids) <= 0)
        {
            echo '<div class="error-message">' . Translation :: get('AttemptNotYetFinished') . '</div>';
            return;
        }
        
        $condition = new InCondition(ComplexContentObjectItem :: PROPERTY_ID, $question_cids, ComplexContentObjectItem :: get_table_name());
        $questions_cloi = $rdm->retrieve_complex_content_object_items($condition);
        
        $total_score = 0;
        $total_weight = 0;
        $question_number = 1;
        
        while ($question_cloi = $questions_cloi->next_result())
        {
            $result = $results[$question_cloi->get_id()];
            
            $question = $rdm->retrieve_content_object($question_cloi->get_ref());
            $answers = unserialize($result['answer']);
            $feedback = $result['feedback'];
            
            $question_cloi->set_ref($question);
            
            $score = $result['score'];
            $score = round($score * 100) / 100;
            
            $total_score += $score;
            $total_weight += $question_cloi->get_weight();
            
            $display = QuestionResultDisplay :: factory($form, $question_cloi, $question_number, $answers, $score, $feedback, $this->get_parent()->can_change_answer_data());
            $display->display();
            
            $question_number ++;
        
        }
        
        if ($form->validate())
        {
            $values = $form->exportValues();
            
            $question_forms = array();
            foreach ($values as $key => $value)
            {
                $split = split('_', $key);
                if (is_numeric($split[0]))
                {
                    $question_forms[$split[0]][$split[1]] = $value;
                }
            }
            
            $total_score = 0;
            
            foreach ($question_forms as $question_id => $question_form)
            {
                $score = $question_form['score'];
                $feedback = $question_form['feedback'];
                
                $this->change_answer_data($question_id, $score, $feedback);
                
                $total_score += $score;
            }
            
            $percent = round(($total_score / $total_weight) * 100);
            $this->change_total_score($percent);
        }
        
        $html[] = '<div class="question">';
        $html[] = '<div class="title">';
        $html[] = '<div class="text">';
        $html[] = '<div class="bevel" style="float: left;">';
        $html[] = Translation :: get('TotalScore');
        $html[] = '</div>';
        $html[] = '<div class="bevel" style="text-align: right;">';
        
        if ($total_score < 0)
            $total_score = 0;
        
        $percent = round(($total_score / $total_weight) * 100);
        
        $html[] = $total_score . ' / ' . $total_weight . ' (' . $percent . '%)';
        $html[] = '</div>';
        
        $html[] = '</div></div></div>';
        $html[] = '<div class="clear"></div>';
        
        $form->addElement('html', implode("\n", $html));
        
        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $form->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        if ($this->get_parent()->can_change_answer_data())
            $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $form->display();
        
        $this->display_footer();
    }
}
?>