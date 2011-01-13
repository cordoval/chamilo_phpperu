<?php
namespace repository\content_object\assessment;

use common\libraries\FormValidator;

use repository\RepositoryDataManager;

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

    function run()
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