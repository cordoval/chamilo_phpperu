<?php
namespace repository\content_object\assessment;

use common\libraries\EqualityCondition;
use common\libraries\Session;
use common\libraries\InCondition;
use HTML_QuickForm_Controller;
use repository\RepositoryDataManager;
use repository\ComplexContentObjectItem;

/**
 * $Id: assessment_viewer_wizard.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer
 */
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__) . '/wizard/assessment_viewer_wizard_display.class.php';
require_once dirname(__FILE__) . '/wizard/assessment_viewer_wizard_process.class.php';
require_once dirname(__FILE__) . '/wizard/assessment_viewer_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/questions_assessment_viewer_wizard_page.class.php';

class AssessmentViewerWizard extends HTML_QuickForm_Controller
{

    private $parent;
    private $assessment;
    private $total_pages;

    function __construct($parent, $assessment)
    {
        parent :: HTML_QuickForm_Controller('AssessmentViewerWizard_' . $parent->get_assessment_current_attempt_id(), true);

        $this->parent = $parent;
        $this->assessment = $assessment;

        $this->addpages();

        $this->addAction('process', new AssessmentViewerWizardProcess($this));
        $this->addAction('display', new AssessmentViewerWizardDisplay($this));
    }

    function addpages()
    {
        $assessment = $this->assessment;
        if ($assessment->get_random_questions() == 0)
        {
            $total_questions = RepositoryDataManager :: get_instance()->count_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $assessment->get_id(), ComplexContentObjectItem :: get_table_name()));
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

        for($i = 1; $i <= $this->total_pages; $i ++)
        {
            $this->addPage(new QuestionsAssessmentViewerWizardPage('question_page_' . $i, $this, $i));
        }
    }

    function get_questions($page_number)
    {
        $assessment = $this->assessment;
        $questions_per_page = $this->assessment->get_questions_per_page();

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

        $questions = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items($condition, array(), $start, $stop);
        return $questions;
    }

    function get_random_questions()
    {
        $questions = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->assessment->get_id(), ComplexContentObjectItem :: get_table_name()));
        while ($question = $questions->next_result())
        {
            $question_list[] = $question;
        }

        $random_questions = array();

        $number_of_random_questions = $this->assessment->get_random_questions();
        if (count($question_list) < $number_of_random_questions)
        {
            foreach ($question_list as $question)
            {
                $random_questions[] = $question->get_id();
            }
        }
        else
        {
            $random_keys = array_rand($question_list, $this->assessment->get_random_questions());

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
        //        exit;
    //
    //        $count = count($question_list);
    //
    //        for($i = 0; $i < $this->assessment->get_random_questions(); $i ++)
    //        {
    //            $random_number = rand(0, $count - 1);
    //
    //            dump($random_number);
    //            dump($question_list[$random_number]);
    //            dump('<hr />');
    //            $random_questions[] = $question_list[$random_number]->get_id();
    //            unset($question_list[$random_number]);
    //            dump($question_list);
    //            dump('<hr />');
    //            dump('<hr />');
    //
    //            $count = count($question_list);
    //        }
    //
    //        return $random_questions;
    }

    function get_parent()
    {
        return $this->parent;
    }

    function get_assessment()
    {
        return $this->assessment;
    }

    function get_total_pages()
    {
        return $this->total_pages;
    }

}
?>