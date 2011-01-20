<?php
namespace repository\content_object\assessment;

use common\libraries\ResourceManager;
use common\libraries\Path;
use common\libraries\Translation;

use repository\content_object\assessment_match_numeric_question\AssessmentMatchNumericQuestion;

/**
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_display
 */
require_once dirname(__FILE__) . '/../question_display.class.php';

class AssessmentMatchNumericQuestionDisplay extends QuestionDisplay
{

    function add_question_form()
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        $clo_question = $this->get_complex_content_object_question();
        $question = $this->get_question();

        $textarea_width = '400px';
        $textarea_height = '50px';
        $textarea_style = 'width: ' . $textarea_width . '; height: ' . $textarea_height . ';';

        $element_template = array();
        $element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode("\n", $element_template);

        $name = $clo_question->get_id() . '_0';
        $formvalidator->addElement('textarea', $name, '', array(
                'style' => $textarea_style));
        $formvalidator->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get_repository_content_object_path(true) . 'assessment/resources/javascript/hint.js'));
        $renderer->setElementTemplate($element_template, $name);
    }

    function add_borders()
    {
        return true;
    }

    function get_instruction()
    {
        $instruction = array();
        $question = $this->get_question();

        if ($question->has_description())
        {
            $instruction[] = '<div class="splitter">';
            $instruction[] = Translation :: get('EnterAnswer');
            $instruction[] = '</div>';
        }
        else
        {
            $instruction = array();
        }

        return implode("\n", $instruction);
    }

    function add_footer($formvalidator)
    {
        $formvalidator = $this->get_formvalidator();

        if ($this->get_question()->has_hint())
        {
            $hint_name = 'hint_' . $this->get_complex_content_object_question()->get_id();

            $html[] = '<div class="splitter">' . Translation :: get('Hint') . '</div>';
            $html[] = '<div class="with_borders"><a id="' . $hint_name . '" class="button hint_button">' . Translation :: get('GetAHint') . '</a></div>';

            $footer = implode("\n", $html);
            $formvalidator->addElement('html', $footer);
        }

        parent :: add_footer($formvalidator);
    }
}