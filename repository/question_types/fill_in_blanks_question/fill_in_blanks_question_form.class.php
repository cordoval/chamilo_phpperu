<?php
/**
 * $Id: fill_in_blanks_question_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.fill_in_blanks_question
 */
require_once dirname(__FILE__) . '/fill_in_blanks_question.class.php';
require_once dirname(__FILE__) . '/fill_in_blanks_question_answer.class.php';

class FillInBlanksQuestionForm extends ContentObjectForm
{
    const DEFAULT_SIZE = 20;

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('AnswerOptions'));

        $type_options = array();
        $type_options[] = $this->createElement('radio', FillInBlanksQuestion :: PROPERTY_QUESTION_TYPE, null, Translation :: get('SelectBox'), FillInBlanksQuestion :: TYPE_SELECT);
        $type_options[] = $this->createElement('radio', FillInBlanksQuestion :: PROPERTY_QUESTION_TYPE, null, Translation :: get('TextField'), FillInBlanksQuestion :: TYPE_TEXT);
        $this->addElement('group', null, Translation :: get('UseSelectBox'), $type_options, '<br />', false);

        $this->addElement('html', '<div class="normal-message">' . Translation :: get('FillInTheblanksInfo') . '</div>');
        $this->addElement('textarea', 'answer', Translation :: get('QuestionText'), 'rows="10" class="answer"');
        $this->addRule('answer', Translation :: get('ThisFieldIsRequired'), 'required');
        $this->add_options();
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get('AnswerOptions'));

        $type_options = array();
        $type_options[] = $this->createElement('radio', FillInBlanksQuestion :: PROPERTY_QUESTION_TYPE, null, Translation :: get('SelectBox'), FillInBlanksQuestion :: TYPE_SELECT);
        $type_options[] = $this->createElement('radio', FillInBlanksQuestion :: PROPERTY_QUESTION_TYPE, null, Translation :: get('TextField'), FillInBlanksQuestion :: TYPE_TEXT);
        $this->addElement('group', null, Translation :: get('UseSelectBox'), $type_options, '<br />', false);

        $this->addElement('html', '<div class="information-message">' . Translation :: get('FillInTheblanksInfo') . '</div>');
        $this->addElement('textarea', 'answer', Translation :: get('QuestionText'), 'rows="10" class="answer"');
        $this->addRule('answer', Translation :: get('ThisFieldIsRequired'), 'required');
        $this->setDefaults();
        $this->add_options();
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {

        if (! $this->isSubmitted())
        {
            $object = $this->get_content_object();
            if (! is_null($object))
            {
                $options = $object->get_answers();
                foreach ($options as $index => $option)
                {
                    $defaults['match_weight'][$index] = $option->get_weight() ? $option->get_weight() : 0;
                    $defaults['comment'][$index] = $option->get_comment();
                    $defaults['size'][$index] = $option->get_size();
                    $defaults['position'][$index] = $option->get_position();
                }
                $defaults['answer'] = $object->get_answer_text();
                $defaults[FillInBlanksQuestion :: PROPERTY_QUESTION_TYPE] = $object->get_question_type();
            }
            else
            {
                $defaults[FillInBlanksQuestion :: PROPERTY_QUESTION_TYPE] = FillInBlanksQuestion :: TYPE_TEXT;
            }

            parent :: setDefaults($defaults);
            return;
        }

        if (! $this->validate())
        {
            for($option_number = 0; $option_number < count($defaults['match']); $option_number ++)
            {
                $defaults['match_weight'][$option_number] = 1;
                $defaults['comment'][$option_number] = '';
                $defaults['size'][$option_number] = self :: DEFAULT_SIZE;
                $defaults['position'][$option_number] = 0;
            }

            parent :: setConstants($defaults);
        }
    }

    function create_content_object($object)
    {
        $values = $this->exportValues();

        //$object = new FillInBlanksQuestion();
        $this->set_content_object($object);
        $object->set_answer_text($values['answer']);
        $object->set_question_type($values[FillInBlanksQuestion :: PROPERTY_QUESTION_TYPE]);
        $this->add_options_to_object();
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $values = $this->exportValues();
        $object = $this->get_content_object();
        $object->set_answer_text($values['answer']);
        $object->set_question_type($values[FillInBlanksQuestion :: PROPERTY_QUESTION_TYPE]);

        $this->add_options_to_object();
        return parent :: update_content_object();
    }

    function get_matches($source)
    {
        $linefeeds = array("\r\n", "\n", "\r");
        $source = str_replace($linefeeds, ' ', $source);

        $matches = array();
        preg_match_all('/\[[a-zA-Z0-9_êëûüôöîïéèà\s\-\x22\x27]*\]/', $source, $matches, PREG_OFFSET_CAPTURE);

        $results = array();

        foreach ($matches[0] as $match)
        {
            $results[$match[1]] = $match[0];
        }

        return $results;
    }

    function validate()
    {
        if (isset($_POST['add']))
        {
            return false;
        }
        return parent :: validate();
    }

    /**
     * Adds the form-fields to the form to provide the possible options for this
     * multiple choice question
     */
    private function add_options()
    {
        $values = $this->exportValues();
        $renderer = $this->defaultRenderer();

        $matches = array();
        preg_match_all('/\[[a-zA-Z0-9_êëûüôöîïéèà\s\-\x22\x27]*\]/', $values['answer'], $matches, PREG_OFFSET_CAPTURE);
        $matches = $matches[0];

        $buttons = array();
        $buttons[] = $this->createElement('style_button', 'add[]', Translation :: get('RefreshBlanks'), array('class' => 'normal refresh add_matches'));
        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $visible = (count($matches) == 0) ? 'display: none;' : '';

        $table_header = array();
        $table_header[] = '<div id="answers_table" class="row" style="' . $visible . '">';
        $table_header[] = '<div class="label">';
        $table_header[] = Translation :: get('Answers');
        $table_header[] = '</div>';
        $table_header[] = '<div class="formw">';
        $table_header[] = '<div class="element">';

        $table_header[] = '<table class="data_table" style="width: 661px;">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th></th>';
        $table_header[] = '<th>' . Translation :: get('Blank') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $table_header[] = '<th class="numeric">' . Translation :: get('Score') . '</th>';
        $table_header[] = '<th class="numeric">' . Translation :: get('Size') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));

        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['show_toolbar'] = false;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';

        for($option_number = 0; $option_number < count($matches); $option_number ++)
        {
            $group = array();

            $group[] = $this->createElement('static', null, null, $option_number + 1);
            $element = $this->createElement('text', 'match[' . $option_number . ']', Translation :: get('Match'), 'style="width: 90%;" ');
            $element->freeze();
            $group[] = $element;

            $element = $this->createElement('hidden', 'position[' . $option_number . ']', null);
            $element->freeze();
            $group[] = $element;

            $group[] = $this->create_html_editor('comment[' . $option_number . ']', Translation :: get('Comment'), $html_editor_options);
            $group[] = $this->createElement('text', 'match_weight[' . $option_number . ']', Translation :: get('Weight'), 'size="2"');
            $group[] = $this->createElement('text', 'size[' . $option_number . ']', Translation :: get('Size'), 'size="2"');

            $this->addGroup($group, 'option_' . $option_number, null, '', false);

            $this->addGroupRule('option_' . $option_number, array('match_weight[' . $option_number . ']' => array(array(Translation :: get('ThisFieldIsRequired'), 'required'), array(Translation :: get('ValueShouldBeNumeric'), 'numeric')), 'size[' . $option_number . ']' => array(array(Translation :: get('ThisFieldIsRequired'), 'required'), array(Translation :: get('ValueShouldBeNumeric'), 'numeric'))));

            $renderer->setElementTemplate('<tr class="' . ($option_number % 2 == 0 ? 'row_even' : 'row_odd') . '"">{element}</tr>', 'option_' . $option_number);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $option_number);

            $defaults['match'][$option_number] = $matches[$option_number][0];
            $defaults['position'][$option_number] = $matches[$option_number][1];
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $table_footer[] = '</div>';
        $table_footer[] = '<div class="form_feedback"></div></div>';
        $table_footer[] = '<div class="clear">&nbsp;</div>';
        $table_footer[] = '</div>';

        $this->addElement('html', implode("\n", $table_footer));

        $this->setDefaults($defaults);
    }
}
?>