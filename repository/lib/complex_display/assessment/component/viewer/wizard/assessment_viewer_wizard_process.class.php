<?php
/**
 * $Id: assessment_viewer_wizard_process.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard
 */
require_once dirname(__FILE__) . '/inc/score_calculator.class.php';
require_once dirname(__FILE__) . '/inc/question_result_display.class.php';

class AssessmentViewerWizardProcess extends HTML_QuickForm_Action
{
    private $parent;

    public function AssessmentViewerWizardProcess($parent)
    {
        $this->parent = $parent;
    }

    function perform($page, $actionName)
    {
        $this->parent->get_parent()->display_header();

        echo '<div class="assessment">';
        echo '<h2>' . Translation :: get('ResultsFor') . ': ' . $this->parent->get_assessment()->get_title() . '</h2>';

        if ($this->parent->get_assessment()->has_description())
        {
            echo '<div class="description">';
            echo $this->parent->get_assessment()->get_description();
            echo '<div class="clear"></div>';
            echo '</div>';
        }
        echo '</div>';

        $values = $this->parent->exportValues();

        foreach ($values as $key => $value)
        {
            $value = Security :: remove_XSS($value);
            $split_key = split('_', $key);
            $question_id = $split_key[0];

            if (is_numeric($question_id))
            {
                $answer_index = $split_key[1];
                $values[$question_id][$answer_index] = $value;
            }
        }

        //$question_numbers = $_SESSION['questions'];


        $rdm = RepositoryDataManager :: get_instance();

        $assessment = $this->parent->get_assessment();
        if ($assessment->get_random_questions() == 0)
        {
            $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $assessment->get_id(), ComplexContentObjectItem :: get_table_name());
        }
        else
        {
            $condition = new InCondition(ComplexContentObjectItem :: PROPERTY_ID, $_SESSION['questions'], ComplexContentObjectItem :: get_table_name());
        }

        $questions_cloi = $rdm->retrieve_complex_content_object_items($condition);

        $question_number = 1;
        $total_score = 0;
        $total_weight = 0;

        while ($question_cloi = $questions_cloi->next_result())
        {
            $question = $rdm->retrieve_content_object($question_cloi->get_ref());
            $answers = $values[$question_cloi->get_id()];
            $question_cloi->set_ref($question);

            $score_calculator = ScoreCalculator :: factory($question, $answers, $question_cloi->get_weight());
            $score = $score_calculator->calculate_score();
            $total_score += $score;
            $total_weight += $question_cloi->get_weight();

            $display = QuestionResultDisplay :: factory($question_cloi, $question_number, $answers, $score);
            $display->display();

            $question_number ++;

            $this->parent->get_parent()->save_answer($question_cloi->get_id(), serialize($answers), $score);

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

        echo implode("\n", $html);

        $this->parent->get_parent()->finish_assessment($percent);

        unset($_SESSION['questions']);

        $back_url = $this->parent->get_parent()->get_go_back_url();

        echo '<a href="' . $back_url . '">' . Translation :: get('GoBack') . '</a>';

        $this->parent->get_parent()->display_footer();
    }
}
?>