<?php
namespace repository\content_object\assessment;

use common\libraries\StringUtilities;

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

    function get_total_pages()
    {
        return $this->assessment_result_processor->get_assessment_viewer()->get_total_pages();
    }

    function add_general()
    {

        $current_page = self :: PAGE_NUMBER . '-' . $this->get_page_number();
        $this->addElement('hidden', $current_page, $this->get_page_number());
    }

    function add_buttons()
    {
        //$progress = round(($this->get_page_number() / $this->get_total_pages()) * 100);
        //Display::get_progress_bar($progress)
        $this->addElement('html', '<div style="float: left; padding: 7px; font-weight: bold; line-height: 100%;">' . Translation :: get('PageNumberOfTotal', array('CURRENT' => $this->get_page_number(), 'TOTAL' => $this->get_total_pages())) . '</div>');

        if (($this->get_page_number() < $this->assessment_result_processor->get_assessment_viewer()->get_total_pages()))
        {
            $buttons[] = $this->createElement('style_submit_button', 'next', Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES), array(
                    'class' => 'normal next'));
        }
        elseif ($this->get_page_number() == $this->assessment_result_processor->get_assessment_viewer()->get_total_pages())
        {

            $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('ViewResults', null, Utilities :: COMMON_LIBRARIES), array(
                    'class' => 'positive finish'));
        }
        elseif ($this->get_page_number() == ($this->assessment_result_processor->get_assessment_viewer()->get_total_pages() + 1))
        {
            $back_url = $this->assessment_result_processor->get_assessment_viewer()->get_assessment_back_url();
            $continue_url = $this->assessment_result_processor->get_assessment_viewer()->get_assessment_continue_url();

            if (! StringUtilities :: is_null_or_empty($continue_url))
            {
                $buttons[] = $this->createElement('static', null, null, '<a href="' . $continue_url . '" target="_parent" class="button normal_button">' . Translation :: get('Continue') . '</a>');
            }

            if (! StringUtilities :: is_null_or_empty($back_url))
            {
                $buttons[] = $this->createElement('static', null, null, '<a href="' . $back_url . '" target="_parent" class="button positive_button finish_button">' . Translation :: get('Finish') . '</a>');
            }
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