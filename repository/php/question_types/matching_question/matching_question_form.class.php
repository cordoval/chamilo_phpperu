<?php
namespace repository;

use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Utilities;
use repository\ContentObject;

/**
 * $Id: matching_question_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.matching_question
 */
require_once dirname(__FILE__) . '/matching_question.class.php';

class MatchingQuestionForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->build_options_and_matches();
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->build_options_and_matches();
    }

    /**
     * Adds the options and matches to the form
     */
    private function build_options_and_matches()
    {
        $this->update_number_of_options_and_matches();
        $this->add_options();
        $this->add_matches();
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();

//        dump($object->get_options());
//        dump($object->get_matches());

        if ($object->get_number_of_options() != 0)
        {
            $options = $object->get_options();
            foreach ($options as $index => $option)
            {
                $defaults[MatchingQuestionOption::PROPERTY_VALUE][$index] = $option->get_value();
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
        }

        parent :: setDefaults($defaults);
    }

    function setCsvValues($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[1];
        parent :: setValues($defaults);
    }

    function create_content_object($object)
    {
        $this->set_content_object($object);
        $this->add_answer();
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $this->add_answer();
        return parent :: update_content_object();
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
            $options[] = new MatchingQuestionOption($value);
        }

        foreach ($values['match'] as $match)
        {
            $matches[] = $match;
        }
        $object->set_options($options);
        $object->set_matches($matches);
    }

    function validate()
    {
        if (isset($_POST['add_match']) || isset($_POST['remove_match']) || isset($_POST['remove_option']) || isset($_POST['add_option']))
        {
            return false;
        }
        return parent :: validate();
    }

    /**
     * Updates the session variables to keep track of the current number of
     * options and matches.
     * @todo This code needs some cleaning :)
     */
    function update_number_of_options_and_matches()
    {
        if (! $this->isSubmitted())
        {
            unset($_SESSION['mq_number_of_options']);
            unset($_SESSION['mq_skip_options']);
            unset($_SESSION['mq_number_of_matches']);
            unset($_SESSION['mq_skip_matches']);
        }
        if (! isset($_SESSION['mq_number_of_options']))
        {
            $_SESSION['mq_number_of_options'] = 3;
        }
        if (! isset($_SESSION['mq_skip_options']))
        {
            $_SESSION['mq_skip_options'] = array();
        }
        if (isset($_POST['add_option']))
        {
            $_SESSION['mq_number_of_options'] = $_SESSION['mq_number_of_options'] + 1;
        }
        if (isset($_POST['remove_option']))
        {
            $indexes = array_keys($_POST['remove_option']);
            $_SESSION['mq_skip_options'][] = $indexes[0];
        }
        if (! isset($_SESSION['mq_number_of_matches']))
        {
            $_SESSION['mq_number_of_matches'] = 3;
        }
        if (! isset($_SESSION['mq_skip_matches']))
        {
            $_SESSION['mq_skip_matches'] = array();
        }
        if (isset($_POST['add_match']))
        {
            $_SESSION['mq_number_of_matches'] = $_SESSION['mq_number_of_matches'] + 1;
        }
        if (isset($_POST['remove_match']))
        {
            $indexes = array_keys($_POST['remove_match']);
            $_SESSION['mq_skip_matches'][] = $indexes[0];
        }
        $object = $this->get_content_object();
        if (! $this->isSubmitted() && $object->get_number_of_options() != 0)
        {
            $_SESSION['mq_number_of_options'] = $object->get_number_of_options();
            $_SESSION['mq_number_of_matches'] = $object->get_number_of_matches();
        }
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
        $table_header[] = '<th>' . Translation :: get('Options') . '</th>';
//        $table_header[] = '<th class="code">' . Translation :: get('Matches') . '</th>';
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
        $table_header[] = '<th>' . Translation :: get('Matches') . '</th>';
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

                $this->addGroupRule('match_' . $match_number, array('match[' . $match_number . ']' => array(array(Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required'))));
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