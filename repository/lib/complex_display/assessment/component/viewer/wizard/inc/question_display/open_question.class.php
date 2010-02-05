<?php
/**
 * $Id: open_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_display
 */
require_once dirname(__FILE__) . '/../question_display.class.php';

class OpenQuestionDisplay extends QuestionDisplay
{

    function add_question_form()
    {
        $clo_question = $this->get_clo_question();
        $question = $this->get_question();
        $type = $question->get_question_type();
        $formvalidator = $this->get_formvalidator();

        switch ($type)
        {
            case OpenQuestion :: TYPE_DOCUMENT :
                $this->add_document($clo_question, $formvalidator);
                break;
            case OpenQuestion :: TYPE_OPEN :
                $this->add_html_editor($clo_question, $formvalidator);
                break;
            case OpenQuestion :: TYPE_OPEN_WITH_DOCUMENT :
                $this->add_html_editor($clo_question, $formvalidator);
                $this->add_document($clo_question, $formvalidator);
                break;
        }
    }

    function add_html_editor($clo_question, $formvalidator)
    {
        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = 150;
        $html_editor_options['toolbar'] = 'Assessment';

        $element_template = array();
        $element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode("\n", $element_template);
        $renderer = $this->get_renderer();

        $name = $clo_question->get_id() . '_0';
        $formvalidator->add_html_editor($name, '', false, $html_editor_options);
        $renderer->setElementTemplate($element_template, $name);
    }

    function add_document($clo_question, $formvalidator)
    {
        $name_1 = $clo_question->get_id() . '_1';
        $name_2 = $clo_question->get_id() . '_2';
        $formvalidator->addElement('file', $name_1, '', array('class' => 'select_file'));
        $group = array();
        $group[] = & $formvalidator->createElement('text', ($name_2 . '_text'), '', array('class' => 'select_file_text', 'disabled' => 'disabled', 'style' => 'display: none; width: 200px; height: 20px'));
        $group[] = & $formvalidator->createElement('hidden', $name_2, '', array('id' => $name_2));
        $group[] = & $formvalidator->createElement('style_submit_button', 'select_file', Translation :: get('SelectFile'), array('class' => 'select_file_button positive', 'style' => 'display: none;', 'id' => $clo_question->get_id()));
        $formvalidator->addGroup($group, '');
        $formvalidator->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/repoviewer_popup.js'));
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
}
?>