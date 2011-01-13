<?php
namespace repository\content_object\assessment;

use repository\RepositoryDataManager;

use common\libraries\FormValidator;
use common\libraries\Security;

class AssessmentResultProcessor
{
    /**
     * @var AssessmentDisplayAssessmentViewerComponent
     */
    private $assessment_viewer;

    /**
     * @var array
     */
    private $question_results = array();

    function __construct(AssessmentDisplayAssessmentViewerComponent $assessment_viewer)
    {
        $this->assessment_viewer = $assessment_viewer;
    }

    function get_page_number()
    {
        return $this->get_assessment_viewer()->get_questions_page();
    }

    function save_answers()
    {
        $results_page_number = $this->assessment_viewer->get_questions_page() - 1;
        $questions_cloi = $this->assessment_viewer->get_questions($results_page_number);

        $question_number = $results_page_number * $this->assessment_viewer->get_root_content_object()->get_questions_per_page();

        $values = $_POST;

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

        $rdm = RepositoryDataManager :: get_instance();

        foreach ($questions_cloi as $question_cloi)
        {
            $answers = $values[$question_cloi->get_id()];

            $score_calculator = ScoreCalculator :: factory($question_cloi->get_ref_object(), $answers, $question_cloi->get_weight());
            $score = $score_calculator->calculate_score();
            $total_score += $score;
            $total_weight += $question_cloi->get_weight();

            if ($this->assessment_viewer->get_feedback_per_page())
            {
                $display = QuestionResultDisplay :: factory($question_cloi, $question_number, $answers, $score);
                $this->question_results[] = $display->as_html();
            }

            $question_number ++;
            $this->assessment_viewer->save_assessment_answer($question_cloi->get_id(), serialize($answers), $score);
        }
    }

    function finish_assessment()
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

        $this->parent->get_parent()->save_assessment_result($percent);

        unset($_SESSION['questions']);

        $back_url = $this->parent->get_parent()->get_assessment_go_back_url();

        if ($back_url)
        {
            echo '<a href="' . $back_url . '">' . Translation :: get('GoBack') . '</a>';
        }
    }

    /**
     * @return array
     */
    function get_question_results()
    {
        return $this->question_results;
    }

    /**
     * @return AssessmentDisplayAssessmentViewerComponent
     */
    function get_assessment_viewer()
    {
        return $this->assessment_viewer;
    }

    /**
     * @return string
     */
    function get_results()
    {
        $form = new AssessmentResultViewerForm($this, 'post', $this->assessment_viewer->get_url());
        return $form->toHtml();
    }

    function display_results()
    {
        echo $this->get_results();
    }
}
?>