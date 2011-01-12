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

class AssessmentResultViewerForm extends FormValidator
{
    const FORM_NAME = 'assessment_result_viewer_form';
    const PAGE_NUMBER = 'assessment_result_page_number';

    /**
     * @var AssessmentResultProcessor
     */
    private $assessment_result_processor;

    function __construct(AssessmentResultProcessor $assessment_result_processor, $method = 'post', $action = null)
    {
        parent :: __construct('assessment_result_viewer_form', $method, $action);

        $this->assessment_result_processor = $assessment_result_processor;

        $this->add_general();
        $this->add_buttons();
        $this->add_results();
        $this->add_buttons();
    }

    function get_page_number()
    {
        return $this->assessment_result_processor->get_assessment_viewer()->get_questions_page();
    }

    function add_general()
    {

        $current_page = self :: PAGE_NUMBER . '-' . $this->get_page_number();
        $this->addElement('hidden', $current_page, $this->get_page_number());
    }

    function add_buttons()
    {
        if (($this->get_page_number() < $this->assessment_result_processor->get_assessment_viewer()->get_total_pages()))
        {
            $buttons[] = $this->createElement('style_submit_button', 'next', Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES), array(
                    'class' => 'normal next'));
        }
        else
        {
            $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Finish', null, Utilities :: COMMON_LIBRARIES), array(
                    'class' => 'positive finish'));
        }

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $renderer = $this->defaultRenderer();
        $renderer->setElementTemplate('<div style="float: right;">{element}</div><br /><br />', 'buttons');
        $renderer->setGroupElementTemplate('{element}', 'buttons');
    }

    function add_results()
    {
        $question_results = $this->assessment_result_processor->get_question_results();
        $question_results = implode("\n", $question_results);
        $this->addElement('html', $question_results);
    }
}
?>