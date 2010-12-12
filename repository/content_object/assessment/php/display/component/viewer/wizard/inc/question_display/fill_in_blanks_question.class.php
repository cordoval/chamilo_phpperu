<?php
namespace repository\content_object\assessment;

use repository\ContentObject;
use common\libraries\Path;
use common\libraries\ResourceManager;
use common\libraries\Translation;
use common\libraries\Theme;

use repository\content_object\fill_in_blanks_question\FillInBlanksQuestion;
use repository\content_object\fill_in_blanks_question\FillInBlanksQuestionAnswer;

/**
 * $Id: fill_in_blanks_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_display
 */
require_once dirname(__FILE__) . '/../question_display.class.php';

class FillInBlanksQuestionDisplay extends QuestionDisplay
{

    function add_question_form()
    {
        $clo_question = $this->get_complex_content_object_question();

        $question = $this->get_question();
        $answers = $question->get_answers();
        $question_type = $question->get_question_type();
        $answer_text = $question->get_answer_text();
        $answer_text = nl2br($answer_text);

        $parts = preg_split(FillInBlanksQuestionAnswer :: QUESTIONS_REGEX, $answer_text);

        $this->add_html('<div class="with_borders">');
        $this->add_html('<div class="fill_in_the_blanks_text">');
        $this->add_html(array_shift($parts));

        $element_template = ' {element} ';
        $renderer = $this->get_renderer();
        $renderer->setElementTemplate($element_template, 'select');

        $this->get_formvalidator()->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get_repository_content_object_path(true) . 'fill_in_blanks_question/resources/javascript/hint.js'));

        $index = 0;
        foreach ($parts as $part)
        {
            $name = $clo_question->get_id() . "[$index]";

            $formvalidator = $this->get_formvalidator();
            $this->add_html('<span class="fill_in_the_blanks_gap">' . Translation :: get('GapNumber', array(
                    'NUMBER' => ($index + 1)), ContentObject :: get_content_object_type_namespace($question->get_type_name())) . '</span>');

            //            $this->add_question($name, $index, $question_type, $answers);
            $this->add_html($part);
            $index ++;

     //$renderer->setElementTemplate($element_template, $name);
        }
        $this->add_html('</div>');
        $this->add_html('<div class="clear"></div>');
        $this->add_html('</div>');

        $table_header = array();
        $table_header[] = '<table class="data_table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="checkbox"></th>';
        $table_header[] = '<th>' . Translation :: get('Answers', null, ContentObject :: get_content_object_type_namespace($question->get_type_name())) . '</th>';
        $table_header[] = '<th>' . Translation :: get('Hint') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->add_html($table_header);

        $index = 0;
        foreach ($parts as $part)
        {
            $name = $clo_question->get_id() . "[$index]";
            $this->add_question($name, $index, $question_type, $answers);
            $index ++;

            $renderer->setElementTemplate('<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'option_' . $index);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $index);
        }

        $table_footer = array();
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->add_html($table_footer);
    }

    function add_html($html)
    {
        $html = is_array($html) ? implode("\n", $html) : $html;
        $formvalidator = $this->get_formvalidator();
        $formvalidator->addElement('html', $html);
    }

    function add_select($name, $options)
    {
        $formvalidator = $this->get_formvalidator();
        return $formvalidator->createElement('select', $name, '', $options);
    }

    function add_text($name, $size)
    {
        // TODO: Making this box a specific width is a hint in and of itself ...
        $size = 75;
        $formvalidator = $this->get_formvalidator();
        return $formvalidator->createElement('text', $name, null, array(
                'size' => $size));
    }

    function add_question($name, $index, $question_type, $answers)
    {
        $formvalidator = $this->get_formvalidator();

        $group = array();
        $group[] = $formvalidator->createElement('static', null, null, ($index + 1));

        $options = $this->get_question_options($index, $answers);
        if ($question_type == FillInBlanksQuestion :: TYPE_SELECT)
        {
            $group[] = $this->add_select($name, $options);
        }
        else
        {
            $size = 0;
            foreach ($options as $option)
            {
                $size = max($size, strlen($option));
            }
            $size = empty($size) ? 20 : $size;
            $group[] = $this->add_text($name, $size);
        }

        $hint_name = 'hint_' . $this->get_complex_content_object_question()->get_id() . '_' . $index;
        $group[] = $formvalidator->createElement('static', null, null, '<a id="' . $hint_name . '" class="button hint_button">' . Translation :: get('GetAHint') . '</a>');

        $formvalidator->addGroup($group, 'option_' . $index, null, '', false);
    }

    function get_question_options($index, $answers)
    {
        $result = array();
        foreach ($answers as $answer)
        {
            if ($answer->get_position() == $index)
            {
                $option = $answer->get_value();
                $result[$option] = $option;
            }
        }
        $this->shuffle_with_keys($result);
        return $result;
    }

    function add_borders()
    {
        return false;
    }

    function get_instruction()
    {
        $instruction = array();
        return implode("\n", $instruction);
    }

    function get_title()
    {
        return Translation :: get('FillInTheBlanks');
    }

    function get_description()
    {
        $html = array();

        if ($this->get_question()->has_description())
        {
            $html[] = '<div class="description">';
            $html[] = $this->get_question()->get_description();
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div>';
            $html[] = '<div class="splitter">' . Translation :: get('QuestionText', null, ContentObject :: get_content_object_type_namespace($this->get_question()->get_type_name())) . '</div>';
        }

        return implode("\n", $html);
    }
}
?>