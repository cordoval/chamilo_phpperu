<?php
namespace repository\content_object\assessment;

use common\libraries\Request;

use common\libraries\Security;
use common\libraries\InCondition;
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
     * The form that displays the questions per page
     * @var AssessmentViewerForm
     */
    private $question_form;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        //var_dump($this->get_feedback_display_configuration());

        if ($this->question_form_submitted())
        {
            $result_processor = new AssessmentResultProcessor($this);
            $result_processor->run();
        }

        if ($this->question_form_submitted() && $this->get_feedback_per_page())
        {
            $this->display_header();
            $result_processor->display_results();
            $this->display_footer();
        }
        else
        {
            $this->question_form = new AssessmentViewerForm($this, 'post', $this->get_url());
            $this->display_header();
            $this->question_form->display();
            $this->display_footer();
        }
    }

    function get_question_form()
    {
        return $this->question_form;
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
        if (! isset($this->total_pages))
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
        if (! isset($this->questions[$page_number]))
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
                $stop = (int) $questions_per_page;
            }

            $this->questions[$page_number] = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items($condition, array(), $start, $stop)->as_array();
        }

        return $this->questions[$page_number];
    }

    function result_form_submitted()
    {
        return ! is_null(Request :: post('_qf__' . AssessmentResultViewerForm :: FORM_NAME));
    }

    function question_form_submitted()
    {
        return ! is_null(Request :: post('_qf__' . AssessmentViewerForm :: FORM_NAME));
    }

    function get_action()
    {
        $actions = array('next', 'submit', 'back');

        foreach ($actions as $action)
        {
            if (! is_null(Request :: post($action)))
            {
                return $action;
            }
        }

        return 'next';
    }

    function get_questions_page()
    {
        if (! $this->current_page)
        {
            if ($this->result_form_submitted() || $this->question_form_submitted())
            {
                if ($this->question_form_submitted() && $this->get_feedback_per_page())
                {
                    // Submitted page number, but results page
                    $this->current_page = $this->get_submitted_page_number();
                }
                else
                {
                    // Submitted page number + 1
                    if ($this->get_action() == 'back')
                    {
                        $this->current_page = $this->get_submitted_page_number() - 1;
                    }
                    else
                    {
                        $this->current_page = $this->get_submitted_page_number() + 1;
                    }
                }
            }
            else
            {
                $this->current_page = 1;
            }
        }

        return $this->current_page;
    }

    function get_previous_questions_page()
    {
        if (! $this->previous_page)
        {
            if ($this->result_form_submitted() || $this->question_form_submitted())
            {
                $this->previous_page = $this->get_submitted_page_number() - 1;
            }
            else
            {
                $this->previous_page = 1;
            }
        }

        return $this->previous_page;
    }

    function get_submitted_page_number()
    {
        $regex = '/^(' . AssessmentViewerForm :: PAGE_NUMBER . '|' . AssessmentResultViewerForm :: PAGE_NUMBER . ')-([0-9]+)/';
        foreach (array_keys($_REQUEST) as $key)
        {
            if (preg_match($regex, $key, $matches))
            {
                return $matches[2];
            }
        }

        return false;
    }
}
?>