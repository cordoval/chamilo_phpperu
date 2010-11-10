<?php
namespace repository\content_object\assessment_match_text_question;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\ResourceManager;
use common\libraries\Theme;

use repository\ContentObjectForm;

/**
 * @package repository.lib.content_object.match_text_question
 */
require_once dirname(__FILE__) . '/main.php';

class AssessmentMatchTextQuestionForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Options'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'repository/content_object/match_question/resources/javascript/match_question.js'));
        $this->add_options();
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get('Options'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'repository/content_object/match_question/resources/javascript/match_question.js'));
        $this->add_options();
        $this->addElement('category');
    
    }

    function setDefaults($defaults = array ())
    {
        if (! $this->isSubmitted())
        {
            $object = $this->get_content_object();
            if ($object->get_number_of_options() != 0)
            {
                $options = $object->get_options();
                foreach ($options as $index => $option)
                {
                    $defaults['option'][$index] = $option->get_value();
                    $defaults['option_weight'][$index] = $option->get_score();
                    $defaults['comment'][$index] = $option->get_feedback();
                }
                $defaults['use_wildcards'] = $object->get_use_wildcards();
                $defaults['ignore_case'] = $object->get_ignore_case();
            }
            else
            {
                $defaults['use_wildcards'] = true;
                $defaults['ignore_case'] = true;
                $number_of_options = intval($_SESSION['match_number_of_options']);
                
                for($option_number = 0; $option_number < $number_of_options; $option_number ++)
                {
                    $defaults['option_weight'][$option_number] = 1;
                }
            }
        }
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $object = new AssessmentMatchTextQuestion();
        $this->set_content_object($object);
        $this->add_options_to_object();
        $result = parent :: create_content_object();
        return $result;
    }

    function update_content_object()
    {
        $this->add_options_to_object();
        return parent :: update_content_object();
    }

    private function add_options_to_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        $options = array();
        foreach ($values['option'] as $option_id => $value)
        {
            $score = $values['option_weight'][$option_id];
            $feedback = $values['comment'][$option_id];
            $options[] = new AssessmentMatchTextQuestionOption($value, $score, $feedback);
        }
        $object->set_use_wildcards($values['use_wildcards']);
        $object->set_ignore_case($values['ignore_case']);
        $object->set_options($options);
    }

    function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']) || isset($_POST['change_answer_type']))
        {
            return false;
        }
        return parent :: validate();
    }

    /**
     * Adds the form-fields to the form to provide the possible options for this
     * match question
     */
    private function add_options()
    {
        $renderer = $this->defaultRenderer();
        
        if (! $this->isSubmitted())
        {
            unset($_SESSION['match_number_of_options']);
            unset($_SESSION['match_skip_options']);
        }
        if (! isset($_SESSION['match_number_of_options']))
        {
            $_SESSION['match_number_of_options'] = 3;
        }
        if (! isset($_SESSION['match_skip_options']))
        {
            $_SESSION['match_skip_options'] = array();
        }
        if (isset($_POST['add']))
        {
            $_SESSION['match_number_of_options'] = $_SESSION['match_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['match_skip_options'][] = $indexes[0];
        }
        $object = $this->get_content_object();
        if (! $this->isSubmitted() && $object->get_number_of_options() != 0)
        {
            $_SESSION['match_number_of_options'] = $object->get_number_of_options();
        }
        $number_of_options = intval($_SESSION['match_number_of_options']);
        
        $this->addElement('hidden', 'match_number_of_options', $_SESSION['match_number_of_options'], array('id' => 'match_number_of_options'));
        
        $use_wildcard_group = array();
        $use_wildcard_group[] = & $this->createElement('checkbox', AssessmentMatchTextQuestion :: PROPERTY_USE_WILDCARDS, Translation :: get('UseWildcards')); //, '', array('class' => MultipleChoiceQuestionOption :: PROPERTY_VALUE, 'id' => AssessmentMultipleChoiceQuestionOption :: PROPERTY_CORRECT . '[' . $option_number . ']'));
        $this->addGroup($use_wildcard_group, 'use_wildcards', Translation :: get('UseWildcards'), '', false);
        
        $use_wildcard_group = array();
        $use_wildcard_group[] = & $this->createElement('checkbox', AssessmentMatchTextQuestion :: PROPERTY_IGNORE_CASE, Translation :: get('IgnoreCase')); //, '', array('class' => MultipleChoiceQuestionOption :: PROPERTY_VALUE, 'id' => AssessmentMultipleChoiceQuestionOption :: PROPERTY_CORRECT . '[' . $option_number . ']'));
        $this->addGroup($use_wildcard_group, 'ignore_case', Translation :: get('IgnoreCase'), '', false);
        
        $buttons = array();
        //Notice: The [] are added to this element name so we don't have to deal with the _x and _y suffixes added when clicking an image button
        $buttons[] = $this->createElement('style_button', 'add[]', Translation :: get('AddItem'), array('class' => 'normal add', 'id' => 'add_option'));
        $this->addGroup($buttons, 'question_buttons', null, '', false);
        
        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['toolbar'] = 'RepositoryQuestion';
        
        $table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th>' . Translation :: get('PossibleAnswer') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $table_header[] = '<th class="numeric">' . Translation :: get('Score') . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));
        
        $textarea_height = $html_editor_options['height'];
        $textarea_width = $html_editor_options['width'];
        
        if (strpos($textarea_height, '%') === false)
        {
            $textarea_height .= 'px';
        }
        if (strpos($textarea_width, '%') === false)
        {
            $textarea_width .= 'px';
        }
        
        $i = 1;
        
        for($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['match_skip_options']))
            {
                $group = array();
                
                $group[] = & $this->createElement('static', null, null, $i . '.');
                $group[] = $this->createElement('textarea', "option[$option_number]", Translation :: get('Answer'), array('style' => 'width: 100%; height:' . $textarea_height));
                $group[] = $this->create_html_editor("comment[$option_number]", Translation :: get('Comment'), $html_editor_options);
                $group[] = & $this->createElement('text', "option_weight[$option_number]", Translation :: get('Weight'), 'size="2"  class="input_numeric"');
                
                if ($number_of_options - count($_SESSION['match_skip_options']) > 2)
                {
                    $group[] = & $this->createElement('image', 'remove[' . $option_number . ']', Theme :: get_common_image_path() . 'action_delete.png', array('class' => 'remove_option', 'id' => $option_number));
                }
                else
                {
                    $group[] = & $this->createElement('static', null, null, '<img src="' . Theme :: get_common_image_path() . 'action_delete_na.png" class="remove_option" />');
                }
                
                $this->addGroup($group, 'option_' . $option_number, null, '', false);
                
                $renderer->setElementTemplate('<tr id="option_' . $option_number . '" class="' . ($option_number % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'option_' . $option_number);
                $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $option_number);
                
                $i ++;
            }
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode("\n", $table_footer));
        
        $renderer->setElementTemplate('<div style="margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>', 'question_buttons');
        $renderer->setGroupElementTemplate('<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 'question_buttons');
        
        $buttons = array();
        //Notice: The [] are added to this element name so we don't have to deal with the _x and _y suffixes added when clicking an image button
        $buttons[] = $this->createElement('style_button', 'add[]', Translation :: get('AddItem'), array('class' => 'normal add', 'id' => 'add_option'));
        $this->addGroup($buttons, 'question_buttons', null, '', false);
        
        $this->setDefaults($defaults);
    }
}
?>