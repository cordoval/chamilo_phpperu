<?php
namespace repository\content_object\assessment;

use repository\ComplexContentObjectItem;

use common\libraries\InCondition;

use common\libraries\EqualityCondition;

use common\libraries\Translation;

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
        $results_page_number = $this->assessment_viewer->get_questions_page();

        if (! $this->assessment_viewer->get_feedback_per_page())
        {
            $results_page_number = $results_page_number - 1;
        }

        $questions_cloi = $this->assessment_viewer->get_questions($results_page_number);

        if ($this->assessment_viewer->get_root_content_object()->get_questions_per_page() == 0)
        {
            $question_number = 1;
        }
        else
        {
            $question_number = ($results_page_number * $this->assessment_viewer->get_root_content_object()->get_questions_per_page());
        }

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

            if ($this->assessment_viewer->get_feedback_per_page())
            {
                $display = QuestionResultDisplay :: factory($this, $question_cloi, $question_number, $answers, $score);
                $this->question_results[] = $display->as_html();
            }

            $question_number ++;

            $tracker = $this->assessment_viewer->get_assessment_question_attempt($question_cloi->get_id());

            if (is_null($tracker))
            {
                $this->assessment_viewer->save_assessment_answer($question_cloi->get_id(), serialize($answers), $score);
            }
            elseif (! is_null($tracker) && ! $this->assessment_viewer->get_feedback_per_page())
            {
                $tracker->set_answer(serialize($answers));
                $tracker->set_score($score);
                $tracker->update();
            }
        }
    }

    function finish_assessment()
    {
        $assessment = $this->get_assessment_viewer()->get_root_content_object();

        $this->question_results[] = '<div class="assessment">';
        $this->question_results[] = '<h2>' . Translation :: get('ResultsFor') . ': ' . $assessment->get_title() . '</h2>';

        if ($assessment->has_description())
        {
            $this->question_results[] = '<div class="description">';
            $this->question_results[] = $assessment->get_description();
            $this->question_results[] = '<div class="clear"></div>';
            $this->question_results[] = '</div>';
        }
        $this->question_results[] = '</div>';

        $rdm = RepositoryDataManager :: get_instance();

        if ($assessment->get_random_questions() == 0)
        {
            $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $assessment->get_id(), ComplexContentObjectItem :: get_table_name());
        }
        else
        {
            $condition = new InCondition(ComplexContentObjectItem :: PROPERTY_ID, $_SESSION['questions'], ComplexContentObjectItem :: get_table_name());
        }

        $questions_cloi = $rdm->retrieve_complex_content_object_items($condition);
        $answers = $this->get_assessment_viewer()->get_assessment_question_attempts();

        $question_number = 1;
        $total_score = 0;
        $total_weight = 0;

        while ($question_cloi = $questions_cloi->next_result())
        {
            $tracker = $answers[$question_cloi->get_id()];
            if (! $tracker)
            {
                continue;
            }

            $score = $tracker->get_score();
            $total_score += $score;
            $total_weight += $question_cloi->get_weight();

            $display = QuestionResultDisplay :: factory($this, $question_cloi, $question_number, unserialize($tracker->get_answer()), $score);
            $this->question_results[] = $display->as_html();

            $question_number ++;
        }

        if ($total_score < 0)
        {
            $total_score = 0;
        }

        $percent = round(($total_score / $total_weight) * 100);
        $this->get_assessment_viewer()->save_assessment_result($percent);

        if ($this->get_assessment_viewer()->display_numeric_feedback())
        {
            $this->question_results[] = '<div class="question">';
            $this->question_results[] = '<div class="title">';
            $this->question_results[] = '<div class="text">';
            $this->question_results[] = '<div class="bevel" style="float: left;">';
            $this->question_results[] = Translation :: get('TotalScore');
            $this->question_results[] = '</div>';
            $this->question_results[] = '<div class="bevel" style="text-align: right;">';

            $this->question_results[] = $total_score . ' / ' . $total_weight . ' (' . $percent . '%)';
            $this->question_results[] = '</div>';

            $this->question_results[] = '</div></div></div>';
            $this->question_results[] = '<div class="clear"></div>';
        }

        unset($_SESSION['questions']);

     //        $back_url = $this->parent->get_parent()->get_assessment_go_back_url();


    //        if ($back_url)
    //        {
    //            echo '<a href="' . $back_url . '">' . Translation :: get('GoBack') . '</a>';
    //        }
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