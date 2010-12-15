<?php
namespace repository\content_object\assessment;

use common\libraries\Session;
use common\libraries\EqualityCondition;

use repository\ComplexContentObjectItem;
use repository\RepositoryDataManager;
/**
 * $Id: assessment_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */
require_once dirname(__FILE__) . '/../assessment_display.class.php';
require_once dirname(__FILE__) . '/viewer/assessment_viewer_wizard.class.php';
require_once dirname(__FILE__) . '/viewer/assessment_viewer_form.class.php';

class AssessmentDisplayAssessmentViewerComponent extends AssessmentDisplay
{
    /**
     * The total number of pages for the assessment
     * @var int
     */
    private $total_pages;

    /**
     * An ObjectResultSet containing all ComplexContentObjectItem
     * objects for individual questions.
     * @var ObjectResultSet
     */
    private $questions;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
//        $form = new AssessmentViewerForm($this, $this->get_root_content_object());
//        $this->display_header();
//        $form->display();
//        $this->display_footer();

        $wizard = new AssessmentViewerWizard($this, $this->get_root_content_object());
        $wizard->run();
    }

    function get_random_questions()
    {
        $assessment = $this->get_root_content_object();
        $questions = $assessment->get_questions();

        while ($question = $questions->next_result())
        {
            $question_list[] = $question;
        }

        $random_questions = array();

        $number_of_random_questions = $assessment->get_random_questions();
        if (count($question_list) < $number_of_random_questions)
        {
            foreach ($question_list as $question)
            {
                $random_questions[] = $question->get_id();
            }
        }
        else
        {
            $random_keys = array_rand($question_list, $assessment->get_random_questions());

            if (! is_array($random_keys))
            {
                $random_keys = array($random_keys);
            }

            foreach ($random_keys as $random_key)
            {
                $random_questions[] = $question_list[$random_key]->get_id();
            }
        }

        return $random_questions;
    }

    function get_assessment()
    {
        return $this->get_root_content_object();
    }

    function get_total_pages()
    {
        if (! isset($total_pages))
        {
            $assessment = $this->get_root_content_object();
            if ($assessment->get_random_questions() == 0)
            {
                $total_questions = $assessment->count_questions();
                Session :: register('questions', 'all');
            }
            else
            {
                $session_questions = Session :: retrieve('questions');

                if (! isset($session_questions) || $session_questions == 'all')
                {
                    Session :: register('questions', $this->get_random_questions());
                }

                $total_questions = $assessment->get_random_questions();
            }

            $questions_per_page = $assessment->get_questions_per_page();

            if ($questions_per_page == 0)
            {
                $this->total_pages = 1;
            }
            else
            {
                $this->total_pages = ceil($total_questions / $questions_per_page);
            }
        }

        return $this->total_pages;
    }

    function get_questions($page_number)
    {
        if (! isset($this->questions))
        {
            $assessment = $this->get_root_content_object();
            $questions_per_page = $assessment->get_questions_per_page();

            $session_questions = Session :: retrieve('questions');

            if ($session_questions == 'all')
            {
                $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $assessment->get_id(), ComplexContentObjectItem :: get_table_name());
            }
            else
            {
                $condition = new InCondition(ComplexContentObjectItem :: PROPERTY_ID, $session_questions, ComplexContentObjectItem :: get_table_name());
            }

            if ($questions_per_page == 0)
            {
                $start = null;
                $stop = null;
            }
            else
            {
                $start = (($page_number - 1) * $questions_per_page);
                $stop = $questions_per_page;
            }

            $this->questions = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items($condition, array(), $start, $stop);
        }

        return $this->questions;
    }
}
?>