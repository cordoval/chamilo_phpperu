<?php
/**
 * $Id: fill_in_blanks_question_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.fill_in_blanks_question
 */
require_once dirname(__FILE__) . '/fill_in_blanks_question.class.php';
require_once dirname(__FILE__) . '/fill_in_blanks_question_answer.class.php';

class FillInBlanksQuestionForm extends ContentObjectForm
{

    protected function build_creation_form(){
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('AnswerOptions'));
        
        $type_options = array();
        $type_options[] = $this->createElement('radio', FillInBlanksQuestion :: PROPERTY_QUESTION_TYPE, null, Translation :: get('SelectBox'), FillInBlanksQuestion :: TYPE_SELECT);
        $type_options[] = $this->createElement('radio', FillInBlanksQuestion :: PROPERTY_QUESTION_TYPE, null, Translation :: get('TextField'), FillInBlanksQuestion :: TYPE_TEXT);
        $this->addElement('group', null, Translation :: get('UseSelectBox'), $type_options, '<br />', false);
        
        $this->addElement('html', '<div class="normal-message">' . Translation :: get('FillInTheblanksInfo') . '</div>');
        $this->addElement('textarea', 'answer', Translation :: get('QuestionText'), 'rows="10" class="answer"');
        $this->addRule('answer', Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/fill_in_the_blanks.js'));
        $this->add_options();
        $this->addElement('category');
    }

    protected function build_editing_form(){
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get('AnswerOptions'));
        
        $type_options = array();
        $type_options[] = $this->createElement('radio', FillInBlanksQuestion :: PROPERTY_QUESTION_TYPE, null, Translation :: get('SelectBox'), FillInBlanksQuestion :: TYPE_SELECT);
        $type_options[] = $this->createElement('radio', FillInBlanksQuestion :: PROPERTY_QUESTION_TYPE, null, Translation :: get('TextField'), FillInBlanksQuestion :: TYPE_TEXT);
        $this->addElement('group', null, Translation :: get('UseSelectBox'), $type_options, '<br />', false);
        
        $this->addElement('html', '<div class="normal-message">' . Translation :: get('FillInTheblanksInfo') . '</div>');
        $this->addElement('textarea', 'answer', Translation :: get('QuestionText'), 'rows="10" class="answer"');
        $this->addRule('answer', Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/fill_in_the_blanks.js'));
        $this->setDefaults();
        $this->add_options();
        $this->addElement('category');
    }

    function setDefaults($defaults = array ()){
        if (! $this->isSubmitted()){
            $object = $this->get_content_object();
            if (! is_null($object)){
                //$options = $object->get_answers();
                $defaults['answer'] = $object->get_answer_text();
                $defaults[FillInBlanksQuestion::PROPERTY_QUESTION_TYPE] = $object->get_question_type();
            }else{
                $defaults['answer'] = '';
                $defaults[FillInBlanksQuestion::PROPERTY_QUESTION_TYPE] = FillInBlanksQuestion::TYPE_TEXT;
            }
            
            parent::setDefaults($defaults);
            return;
        }
    }

    function create_content_object()
    {
        $values = $this->exportValues();
        $object = new FillInBlanksQuestion();
        $this->set_content_object($object);
        $object->set_answer_text($values['answer']);
        $object->set_question_type($values[FillInBlanksQuestion::PROPERTY_QUESTION_TYPE]);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $values = $this->exportValues();
        $object = $this->get_content_object();
        $object->set_answer_text($values['answer']);
        $object->set_question_type($values[FillInBlanksQuestion::PROPERTY_QUESTION_TYPE]);
        return parent::update_content_object();
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
    private function add_options(){
        $values = $this->exportValues();
        $answers = FillInBlanksQuestionAnswer::parse($values['answer']);
        
        $style = (count($answers) == 0) ? 'style="display: none;"' : '';
        
        $html = array();
        $html[] = '<div id="answers_table" class="row" ' . $style . '">';
        $html[] = '<div class="label">';
        $html[] = Translation::get('Answers');
        $html[] = '</div>';
        $html[] = '<div class="formw">';
        $html[] = '<div class="element">';
        $html[] = '<table class="data_table" style="width: 661px;">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th>' . Translation :: get('Question') . '</th>';
        $html[] = '<th>' . Translation :: get('Answer') . '</th>';
        $html[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $html[] = '<th class="numeric">' . Translation :: get('Score') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        $position = 0;
        $css_class = 'row_even';
        foreach($answers as $answer){
			if($answer->get_position() != $position){
				$position = $answer->get_position();
				$css_class = $css_class == 'row_even' ? 'row_odd' : 'row_even';
			}
			$html[] = '<tr class="'. $css_class .'">';
			$html[] = '<td>' . $answer->get_position() . '</td>';
			$html[] = '<td>' . $answer->get_value() . '</td>';
			$html[] = '<td>' . $answer->get_comment() . ' </td>';
			$html[] = '<td>' . $answer->get_weight() . '</td>';
			$html[] = '</tr>';
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        $html[] = '</div>';
        $html[] = '<div class="form_feedback"></div></div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        
        $this->addElement('html', implode("\n", $html));
    }

}