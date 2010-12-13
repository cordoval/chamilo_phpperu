<?php
namespace repository\content_object\assessment;

use common\libraries\FormValidator;
use common\libraries\EqualityCondition;
use common\libraries\Session;
use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\InCondition;
use HTML_QuickForm_Controller;
use repository\RepositoryDataManager;
use repository\ComplexContentObjectItem;

class AssessmentViewerForm extends FormValidator
{
    private $parent;
    private $assessment;
    private $questions;
    private $total_pages;

    function __construct($parent, $assessment, $method = 'post', $action = null)
    {
        parent :: __construct('test', $method, $action);

        $this->parent = $parent;
        $this->assessment = $assessment;
        $this->page_number = 1;
        $this->set_total_pages();

        $this->questions = $this->get_questions($this->page_number);

        $this->add_buttons();
        $this->add_questions();
        $this->add_buttons();
    }

    function add_buttons()
    {
        if ($this->page_number > 1)
            $buttons[] = $this->createElement('style_submit_button', 'back', Translation :: get('Back', null, Utilities :: COMMON_LIBRARIES), array(
                    'class' => 'previous'));

        if ($this->page_number < $this->get_total_pages())
        {
            $style = 'display: none';
            $buttons[] = $this->createElement('style_submit_button', 'process', Translation :: get('Submit', null, Utilities :: COMMON_LIBRARIES), array(
                    'class' => 'positive finish process',
                    'style' => $style));
            $buttons[] = $this->createElement('style_submit_button', 'next', Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES), array(
                    'class' => 'next'));
        }

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Submit', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'positive finish',
                'style' => $style));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $renderer = $this->defaultRenderer();
        $renderer->setElementTemplate('<div style="float: right;">{element}</div><br /><br />', 'buttons');
        $renderer->setGroupElementTemplate('{element}', 'buttons');
    }

    function add_questions()
    {
        $i = (($this->page_number - 1) * $this->get_assessment()->get_questions_per_page()) + 1;

        while ($question = $this->questions->next_result())
        //foreach($this->questions as $question)
        {
            $question_display = QuestionDisplay :: factory($this, $question, $i);
            $question_display->display();
            $i ++;
        }
    }

    function set_total_pages()
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