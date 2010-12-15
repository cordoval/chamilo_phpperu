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

    function __construct($parent, $assessment, $method = 'post', $action = null)
    {
        parent :: __construct('test', $method, $action);

        $this->parent = $parent;
        $this->page_number = 1;

        $this->add_buttons();
        $this->add_questions();
        $this->add_buttons();
    }

    function add_buttons()
    {
        if ($this->page_number > 1)
            $buttons[] = $this->createElement('style_submit_button', 'back', Translation :: get('Back', null, Utilities :: COMMON_LIBRARIES), array(
                    'class' => 'previous'));

        if ($this->page_number < $this->parent->get_total_pages())
        {
            $style = 'display: none';
            $buttons[] = $this->createElement('style_submit_button', 'process', Translation :: get('Submit', null, Utilities :: COMMON_LIBRARIES), array(
                    'class' => 'positive finish process', 'style' => $style));
            $buttons[] = $this->createElement('style_submit_button', 'next', Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES), array(
                    'class' => 'next'));
        }

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Submit', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'positive finish', 'style' => $style));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $renderer = $this->defaultRenderer();
        $renderer->setElementTemplate('<div style="float: right;">{element}</div><br /><br />', 'buttons');
        $renderer->setGroupElementTemplate('{element}', 'buttons');
    }

    function add_questions()
    {
        $i = (($this->page_number - 1) * $this->parent->get_assessment()->get_questions_per_page()) + 1;

        while ($question = $this->parent->get_questions()->next_result())
        {
            $question_display = QuestionDisplay :: factory($this, $question, $i);
            $question_display->display();
            $i ++;
        }
    }
}
?>