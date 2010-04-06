<?php
/**
 * $Id: matrix_question_form.class.php
 * @package repository.question_types.matrix_question
 */
require_once dirname(__FILE__) . '/matrix_question.class.php';

class MatrixQuestionForm extends ContentObjectForm
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
    function build_options_and_matches()
    {
        $this->update_number_of_options_and_matches();
        $this->add_options();
        $this->add_matches();
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        if (! is_null($object))
        {
            $options = $object->get_options();
            foreach ($options as $index => $option)
            {
                $defaults[MatrixQuestionOption::PROPERTY_VALUE][$index] = $option->get_value();
                $defaults[MatrixQuestionOption::PROPERTY_MATCHES][$index] = $option->get_matches();
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
        $this->add_answers();
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $this->add_answers();
        return parent :: update_content_object();
    }

    function remove_XSS_recursive(){}

    /**
     * Adds the answer to the current learning object.
     * This function adds the list of possible options and matches and the
     * relation between the options and the matches to the question.
     */
    function add_answers()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        $options = array();
        $matches = array();

        foreach ($values[MatrixQuestionOption::PROPERTY_VALUE] as $option_id => $value)
        {
            //Create the option with it corresponding match
            $options[] = new MatrixQuestionOption($value, serialize($_POST[MatrixQuestionOption::PROPERTY_MATCHES][$option_id]));
        }

        foreach ($values['match'] as $match)
        {
            $matches[] = $match;
        }
        $object->set_options($options);
        $object->set_matches($matches);
        $object->set_matrix_type($_SESSION['mq_matrix_type']);
    }

    function validate()
    {
        if (isset($_POST['add_match']) || isset($_POST['remove_match']) || isset($_POST['remove_option']) || isset($_POST['add_option']) || isset($_POST['change_matrix_type']))
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
            unset($_SESSION['mq_matrix_type']);
        }

        if (! isset($_SESSION['mq_number_of_options']))
        {
            $_SESSION['mq_number_of_options'] = 3;
        }

        if (! isset($_SESSION['mq_skip_options']))
        {
            $_SESSION['mq_skip_options'] = array();
        }

        if (! isset($_SESSION['mq_matrix_type']))
        {
            $_SESSION['mq_matrix_type'] = MatrixQuestion :: MATRIX_TYPE_RADIO;
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

        if (isset($_POST['change_matrix_type']))
        {
            $_SESSION['mq_matrix_type'] = $_SESSION['mq_matrix_type'] == MatrixQuestion :: MATRIX_TYPE_RADIO ? MatrixQuestion :: MATRIX_TYPE_CHECKBOX : MatrixQuestion :: MATRIX_TYPE_RADIO;
        }

        $object = $this->get_content_object();
        if (! $this->isSubmitted() && ! is_null($object))
        {
            $_SESSION['mq_number_of_options'] = $object->get_number_of_options();
            $_SESSION['mq_number_of_matches'] = $object->get_number_of_matches();
            $_SESSION['mq_matrix_type'] = $object->get_matrix_type();
        }

        $this->addElement('hidden', 'mq_number_of_options', $_SESSION['mq_number_of_options'], array('id' => 'mq_number_of_options'));
        $this->addElement('hidden', 'mq_number_of_matches', $_SESSION['mq_number_of_matches'], array('id' => 'mq_number_of_matches'));
        $this->addElement('hidden', 'mq_matrix_type', $_SESSION['mq_matrix_type'], array('id' => 'mq_matrix_type'));

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

        if ($_SESSION['mq_matrix_type'] == MatrixQuestion :: MATRIX_TYPE_RADIO)
        {
            $switch_label = Translation :: get('SwitchToMultipleMatches');
            $multiple = false;
        }
        elseif ($_SESSION['mq_matrix_type'] == MatrixQuestion :: MATRIX_TYPE_CHECKBOX)
        {
            $switch_label = Translation :: get('SwitchToSingleMatch');
            $multiple = true;
        }

        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', 'change_matrix_type[]', $switch_label, array('class' => 'normal switch change_matrix_type'));
        $buttons[] = $this->createElement('style_button', 'add_option[]', Translation :: get('AddMatrixQuestionOption'), array('class' => 'normal add', 'id' => 'add_option'));
        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer = $this->defaultRenderer();

        $table_header = array();
        $table_header[] = '<table class="data_table options">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th>' . Translation :: get('Options') . '</th>';
        $table_header[] = '<th class="code">' . Translation :: get('Matches') . '</th>';
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
                $group[] = $this->create_html_editor(MatrixQuestionOption::PROPERTY_VALUE . '[' . $option_number . ']', Translation :: get('Answer'), $html_editor_options);
                $group[] = $this->createElement('select', MatrixQuestionOption::PROPERTY_MATCHES . '[' . $option_number . ']', Translation :: get('Matches'), $matches, array('class' => 'option_matches'));
                $group[2]->setMultiple($multiple);

                if ($number_of_options - count($_SESSION['mq_skip_options']) > 2)
                {
                    $group[] = $this->createElement('image', 'remove_option[' . $option_number . ']', Theme :: get_common_image_path() . 'action_delete.png', array('class' => 'remove_option', 'id' => 'remove_option_' . $option_number));
                }
                else
                {
                    $group[] = & $this->createElement('static', null, null, '<img class="remove_option" src="' . Theme :: get_common_image_path() . 'action_delete_na.png" />');
                }

                $this->addGroup($group, MatrixQuestionOption::PROPERTY_VALUE . '_' . $option_number, null, '', false);

                $renderer->setElementTemplate('<tr id="option_' . $option_number . '" class="' . ($visual_number % 2 == 0 ? 'row_odd' : 'row_even') . '">{element}</tr>', MatrixQuestionOption::PROPERTY_VALUE . '_' . $option_number);
                $renderer->setGroupElementTemplate('<td>{element}</td>', MatrixQuestionOption::PROPERTY_VALUE . '_' . $option_number);
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
     * matrix question
     */
    function add_matches()
    {
        $number_of_matches = intval($_SESSION['mq_number_of_matches']);
        $this->addElement('category', Translation :: get('Matches'));

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
                    $group[] = & $this->createElement('static', null, null, '<img class="remove_match" src="' . Theme :: get_common_image_path() . 'action_delete_na.png" />');
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
