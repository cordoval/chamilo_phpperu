<?php
namespace repository\content_object\assessment;

use common\libraries\FormValidator;
use common\libraries\EqualityCondition;
use common\libraries\Session;
use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\InCondition;
use common\libraries\Request;

use HTML_QuickForm_Controller;

use repository\RepositoryDataManager;
use repository\ComplexContentObjectItem;

class AssessmentViewerForm extends FormValidator
{
    const FORM_NAME = 'assessment_viewer_form';
    const PAGE_NUMBER = 'assessment_page_number';

    /**
     * @var AssessmentDisplayAssessmentViewerComponent
     */
    private $assessment_viewer;

    function __construct(AssessmentDisplayAssessmentViewerComponent $assessment_viewer, $method = 'post', $action = null)
    {
        parent :: __construct(self :: FORM_NAME, $method, $action);

        $this->assessment_viewer = $assessment_viewer;

        $this->add_general();
        $this->add_buttons();
        $this->add_questions();
        $this->add_buttons();
    }

    function get_page_number()
    {
        return $this->assessment_viewer->get_questions_page();
    }

    function add_general()
    {

        $current_page = self :: PAGE_NUMBER . '-' . $this->get_page_number();
        $this->addElement('hidden', $current_page, $this->get_page_number());
    }

    function add_buttons()
    {
        if ($this->assessment_viewer->get_feedback_per_page())
        {
            if (($this->get_page_number() < $this->assessment_viewer->get_total_pages()))
            {
                $buttons[] = $this->createElement('style_submit_button', 'next', Translation :: get('Check', null, Utilities :: COMMON_LIBRARIES), array(
                        'class' => 'normal next'));
            }
            else
            {
                $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Finish', null, Utilities :: COMMON_LIBRARIES), array(
                        'class' => 'positive finish'));
            }
        }
        else
        {
            if ($this->get_page_number() > 1)
            {
                $buttons[] = $this->createElement('style_submit_button', 'back', Translation :: get('Back', null, Utilities :: COMMON_LIBRARIES), array(
                        'class' => 'previous'));
            }

            if ($this->get_page_number() < $this->assessment_viewer->get_total_pages())
            {
                $buttons[] = $this->createElement('style_submit_button', 'next', Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES), array(
                        'class' => 'next'));
            }
            else
            {
                $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Submit', null, Utilities :: COMMON_LIBRARIES), array(
                        'class' => 'positive submit'));
            }
        }

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $renderer = $this->defaultRenderer();
        $renderer->setElementTemplate('<div style="float: right;">{element}</div><br /><br />', 'buttons');
        $renderer->setGroupElementTemplate('{element}', 'buttons');
    }

    function add_questions()
    {
        $i = (($this->get_page_number() - 1) * $this->assessment_viewer->get_assessment()->get_questions_per_page()) + 1;

        $questions = $this->assessment_viewer->get_questions($this->get_page_number());

        foreach ($questions as $question)
        {
            $question_display = QuestionDisplay :: factory($this, $question, $i);
            $question_display->display();
            $i ++;
        }
    }
}
?>