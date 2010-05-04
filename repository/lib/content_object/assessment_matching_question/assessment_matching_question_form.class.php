<?php
/**
 * $Id: assessment_matching_question_form.class.php $
 * @package repository.lib.content_object.matching_question
 */
require_once PATH :: get_repository_path() . '/question_types/matching_question/matching_question_form.class.php';
require_once dirname(__FILE__) . '/assessment_matching_question_option.class.php';

class AssessmentMatchingQuestionForm extends MatchingQuestionForm
{
    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/assessment_matching_question.js'));
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/assessment_matching_question.js'));
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        if (! is_null($object))
        {
            $options = $object->get_options();
            foreach ($options as $index => $option)
            {
                $defaults[MatchingQuestionOption::PROPERTY_VALUE][$index] = $option->get_value();
                $defaults[AssessmentMatchingQuestionOption::PROPERTY_SCORE][$index] = $option->get_score();
                $defaults['matches_to'][$index] = $option->get_match();
                $defaults[AssessmentMatchingQuestionOption::PROPERTY_FEEDBACK][$index] = $option->get_feedback();
            }
            $matches = $object->get_matches();
            foreach ($matches as $index => $match)
            {
                $defaults['match'][$index] = $match;
            }
        }
        else
        {
            $number_of_options = intval($_SESSION['mq_number_of_options']);

            for($option_number = 0; $option_number < $number_of_options; $option_number ++)
            {
                $defaults[AssessmentMatchingQuestionOption::PROPERTY_SCORE][$option_number] = 1;
            }
        }

        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $object = new AssessmentMatchingQuestion();
        return parent :: create_content_object($object);
    }

    /**
     * Adds the answer to the current learning object.
     * This function adds the list of possible options and matches and the
     * relation between the options and the matches to the question.
     */
    function add_answer()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        $options = array();
        $matches = array();

        //Get an array with a mapping from the match-id to its index in the $values['match'] array
        $matches_indexes = array_flip(array_keys($values['match']));
        foreach ($values[MatchingQuestionOption::PROPERTY_VALUE] as $option_id => $value)
        {
            //Create the option with it corresponding match
            $options[] = new AssessmentMatchingQuestionOption($value, $matches_indexes[$values['matches_to'][$option_id]], $values[AssessmentMatchingQuestionOption::PROPERTY_SCORE][$option_id], $values[AssessmentMatchingQuestionOption::PROPERTY_FEEDBACK][$option_id]);
        }

        foreach ($values['match'] as $match)
        {
            $matches[] = $match;
        }
        $object->set_options($options);
        $object->set_matches($matches);
    }


    /**
     * Adds the form-fields to the form to provide the possible options for this
     * multiple choice question
     * @todo Add rules to require options and matches
     */
    function add_options()
    {
        $number_of_options = intval($_SESSION['mq_number_of_options']);
        $matches = array();
        $match_label = 'A';

        for($match_number = 0; $match_number < $_SESSION['mq_number_of_matches']; $match_number ++)
        {
            if (! in_array($match_number, $_SESSION['mq_skip_matches']))
            {
                $matches[$match_number] = $match_label ++;
            }
        }

        $this->addElement('category', Translation :: get('Options'));
        $this->addElement('hidden', 'mq_number_of_options', $_SESSION['mq_number_of_options'], array('id' => 'mq_number_of_options'));

        $buttons = array();
        $buttons[] = $this->createElement('style_button', 'add_option[]', Translation :: get('AddMatchingQuestionOption'), array('class' => 'normal add', 'id' => 'add_option'));
        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer = $this->defaultRenderer();

        $table_header = array();
        $table_header[] = '<table class="data_table options">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th>' . Translation :: get('Answer') . '</th>';
        $table_header[] = '<th class="code">' . Translation :: get('Matches') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $table_header[] = '<th class="numeric">' . Translation :: get('Score') . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));

        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';

        $visual_number = 0;

        for($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            $group = array();
            if (! in_array($option_number, $_SESSION['mq_skip_options']))
            {
                $visual_number ++;
                $group[] = $this->createElement('static', null, null, $visual_number);
                $group[] = $this->create_html_editor(MatchingQuestionOption::PROPERTY_VALUE . '[' . $option_number . ']', Translation :: get('Answer'), $html_editor_options);
                $group[] = $this->createElement('select','matches_to[' . $option_number . ']', Translation :: get('Matches'), $matches);
                $group[] = $this->create_html_editor(AssessmentMatchingQuestionOption::PROPERTY_FEEDBACK . '[' . $option_number . ']', Translation :: get('Comment'), $html_editor_options);
                $group[] = $this->createElement('text', AssessmentMatchingQuestionOption::PROPERTY_SCORE . '[' . $option_number . ']', Translation :: get('Weight'), 'size="2"  class="input_numeric"');

                if ($number_of_options - count($_SESSION['mq_skip_options']) > 2)
                {
                    $group[] = $this->createElement('image', 'remove_option[' . $option_number . ']', Theme :: get_common_image_path() . 'action_delete.png', array('class' => 'remove_option', 'id' => 'remove_option_' . $option_number));
                }
                else
                {
                    $group[] = & $this->createElement('static', null, null, '<img class="remove_option" src="' . Theme :: get_common_image_path() . 'action_delete_na.png" />');
                }

                $this->addGroup($group, MatchingQuestionOption::PROPERTY_VALUE . '_' . $option_number, null, '', false);

                $renderer->setElementTemplate('<tr id="option_' . $option_number . '" class="' . ($visual_number % 2 == 0 ? 'row_odd' : 'row_even') . '">{element}</tr>', MatchingQuestionOption::PROPERTY_VALUE . '_' . $option_number);
                $renderer->setGroupElementTemplate('<td>{element}</td>', MatchingQuestionOption::PROPERTY_VALUE . '_' . $option_number);

                $this->addGroupRule(MatchingQuestionOption::PROPERTY_VALUE . '_' . $option_number, array(MatchingQuestionOption::PROPERTY_VALUE . '[' . $option_number . ']' => array(array(Translation :: get('ThisFieldIsRequired'), 'required')), AssessmentMatchingQuestionOption::PROPERTY_SCORE . '[' . $option_number . ']' => array(array(Translation :: get('ThisFieldIsRequired'), 'required'), array(Translation :: get('ValueShouldBeNumeric'), 'numeric'))));
            }
        }
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode("\n", $table_footer));

        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer->setElementTemplate('<div style="margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>', 'question_buttons');
        $renderer->setGroupElementTemplate('<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 'question_buttons');

        $this->addElement('category');
    }

    /**
     * Adds the form-fields to the form to provide the possible matches for this
     * matching question
     */
    function add_matches()
    {
        $number_of_matches = intval($_SESSION['mq_number_of_matches']);
        $this->addElement('category', Translation :: get('Matches'));

        $this->addElement('hidden', 'mq_number_of_matches', $_SESSION['mq_number_of_matches'], array('id' => 'mq_number_of_matches'));

        $buttons = array();
        $buttons[] = $this->createElement('style_button', 'add_match[]', Translation :: get('AddMatch'), array('class' => 'normal add', 'id' => 'add_match'));
        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer = $this->defaultRenderer();

        $table_header = array();
        $table_header[] = '<table class="data_table matches">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th>' . Translation :: get('Answer') . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));

        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';

        $label = 'A';
        for($match_number = 0; $match_number < $number_of_matches; $match_number ++)
        {
            $group = array();

            if (! in_array($match_number, $_SESSION['mq_skip_matches']))
            {
                $defaults['match_label'][$match_number] = $label ++;
                $element = $this->createElement('text', 'match_label[' . $match_number . ']', Translation :: get('Match'), 'style="width: 90%;" ');
                $element->freeze();
                $group[] = $element;
                $group[] = $this->create_html_editor('match[' . $match_number . ']', Translation :: get('Match'), $html_editor_options);

                if ($number_of_matches - count($_SESSION['mq_skip_matches']) > 2)
                {
                    $group[] = $this->createElement('image', 'remove_match[' . $match_number . ']', Theme :: get_common_image_path() . 'action_delete.png', array('class' => 'remove_match', 'id' => 'remove_match_' . $match_number));
                }
                else
                {
                    $group[] = & $this->createElement('static', null, null, '<img src="' . Theme :: get_common_image_path() . 'action_delete_na.png" />');
                }

                $this->addGroup($group, 'match_' . $match_number, null, '', false);

                $renderer->setElementTemplate('<tr id="match_' . $match_number . '" class="' . ($match_number - 1 % 2 == 0 ? 'row_odd' : 'row_even') . '">{element}</tr>', 'match_' . $match_number);
                $renderer->setGroupElementTemplate('<td>{element}</td>', 'match_' . $match_number);

                $this->addGroupRule('match_' . $match_number, array('match[' . $match_number . ']' => array(array(Translation :: get('ThisFieldIsRequired'), 'required'))));
            }

            $this->setConstants($defaults);
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode("\n", $table_footer));

        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer->setElementTemplate('<div style="margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>', 'question_buttons');
        $renderer->setGroupElementTemplate('<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 'question_buttons');

        $this->addElement('category');
    }
}
?>