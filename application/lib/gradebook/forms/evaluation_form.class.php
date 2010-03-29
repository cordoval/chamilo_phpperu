<?php

class EvaluationForm extends FormValidator
{/*
	
	private $auto_generate_evaluation_applications = array('assessment',...);
	
	
	static function build_evaluation_question($form)
	{
		if(array_key_exists($publication, $auto_generate_evaluation_applications))
		{
		$choices = array();
        $choices[] = $this->createElement('checkbox', self :: EVALUATION, '', Translation :: get('Evaluation'));
        $form->addGroup($choices, null, Translation :: get('IsEvaluation'), '<br />', false);
        $form->addElement('html', '<div style="margin-left: 25px; display: block;" id="' . self :: EVALUATION . '_window">');
        $form->addElement('html', '</div>');
{
		}
		else
		{
		$choices = array();
        $choices[] = $this->createElement('checkbox', self :: EVALUATION, '', Translation :: get('Evaluation'));
        $form->addGroup($choices, null, Translation :: get('IsEvaluation'), '<br />', false);
        $form->addElement('html', '<div style="margin-left: 25px; display: block;" id="' . self :: EVALUATION . '_window">');
        $form->addElement('select', Translation :: get('FormatType') ,Translation :: get('FormatType') . ':', DatabaseGradebookDatamanager :: get_instance()->retrieve_all_active_evaluation_formats());
        $form->addElement('html', '</div>');
		}
		return $form;
	}*/
}

?>